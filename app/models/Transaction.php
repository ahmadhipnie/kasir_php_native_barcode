<?php

class Transaction extends Model
{
    protected $table = 'transactions';

    public function createWithItems($transactionData, $items)
    {
        try {
            $this->db->beginTransaction();

            $this->create($transactionData);
            $transactionId = $this->db->lastInsertId();

            $stmtItem = $this->db->prepare("
                INSERT INTO transaction_items
                    (transaction_id, product_id, product_name, barcode, quantity, price, subtotal)
                VALUES
                    (:tid, :pid, :pname, :barcode, :qty, :price, :subtotal)
            ");

            $productModel = new Product();

            foreach ($items as $item) {
                $stmtItem->execute([
                    'tid'      => $transactionId,
                    'pid'      => $item['product_id'],
                    'pname'    => $item['product_name'],
                    'barcode'  => $item['barcode'],
                    'qty'      => $item['quantity'],
                    'price'    => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                if (!$productModel->updateStock($item['product_id'], $item['quantity'], 'decrease')) {
                    throw new Exception('Stok ' . $item['product_name'] . ' tidak mencukupi');
                }
            }

            $this->db->commit();
            return $transactionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getWithItems($id)
    {
        $transaction = $this->find($id);
        if (!$transaction) return null;

        $stmt = $this->db->prepare("
            SELECT ti.id, ti.transaction_id, ti.product_id, ti.quantity, ti.price, ti.subtotal,
                   COALESCE(p.name, ti.product_name) as product_name,
                   COALESCE(p.barcode, ti.barcode) as barcode
            FROM transaction_items ti
            LEFT JOIN products p ON ti.product_id = p.id
            WHERE ti.transaction_id = :tid
        ");
        $stmt->execute(['tid' => $id]);
        $transaction->items = $stmt->fetchAll();

        return $transaction;
    }

    public function getTodaySales()
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total
            FROM {$this->table}
            WHERE DATE(transaction_date) = CURDATE()
        ");
        $stmt->execute();
        return (int) $stmt->fetch()->total;
    }

    public function getTodayCount()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as c
            FROM {$this->table}
            WHERE DATE(transaction_date) = CURDATE()
        ");
        $stmt->execute();
        return (int) $stmt->fetch()->c;
    }

    public function getRecent($limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            ORDER BY transaction_date DESC LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Daily sales for last N days (for chart) */
    public function getDailySales($days = 7)
    {
        $stmt = $this->db->prepare("
            SELECT DATE(transaction_date) as date,
                   COALESCE(SUM(total_amount), 0) as total,
                   COUNT(*) as count
            FROM {$this->table}
            WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL :d DAY)
            GROUP BY DATE(transaction_date)
            ORDER BY date ASC
        ");
        $stmt->bindValue(':d', (int)$days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /** Monthly total for current month */
    public function getMonthlyTotal()
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total
            FROM {$this->table}
            WHERE MONTH(transaction_date) = MONTH(CURDATE())
              AND YEAR(transaction_date) = YEAR(CURDATE())
        ");
        $stmt->execute();
        return (int) $stmt->fetch()->total;
    }

    public function getTotalTransactions()
    {
        return $this->count();
    }

    public function getByDateRange($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, COALESCE(u.name, '-') as cashier_name
            FROM {$this->table} t
            LEFT JOIN users u ON t.user_id = u.id
            WHERE DATE(t.transaction_date) BETWEEN :from AND :to
            ORDER BY t.transaction_date DESC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll();
    }
}
