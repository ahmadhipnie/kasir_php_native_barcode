<?php

class Purchase extends Model
{
    protected $table = 'purchases';

    public function createWithItems($purchaseData, $items)
    {
        try {
            $this->db->beginTransaction();

            $this->create($purchaseData);
            $purchaseId = $this->db->lastInsertId();

            $stmtItem = $this->db->prepare("
                INSERT INTO purchase_items
                    (purchase_id, product_id, product_name, barcode, quantity, price, subtotal)
                VALUES
                    (:pid, :prod_id, :pname, :barcode, :qty, :price, :subtotal)
            ");

            require_once '../app/models/Product.php';
            $productModel = new Product();

            foreach ($items as $item) {
                $stmtItem->execute([
                    'pid'      => $purchaseId,
                    'prod_id'  => $item['product_id'],
                    'pname'    => $item['product_name'],
                    'barcode'  => $item['barcode'],
                    'qty'      => $item['quantity'],
                    'price'    => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                $productModel->updateStock($item['product_id'], $item['quantity'], 'increase');
            }

            $this->db->commit();
            return $purchaseId;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getWithItems($id)
    {
        $purchase = $this->find($id);
        if (!$purchase) return null;

        $stmt = $this->db->prepare("
            SELECT pi.*, COALESCE(p.name, pi.product_name) as product_name,
                   COALESCE(p.barcode, pi.barcode) as barcode
            FROM purchase_items pi
            LEFT JOIN products p ON pi.product_id = p.id
            WHERE pi.purchase_id = :pid
        ");
        $stmt->execute(['pid' => $id]);
        $purchase->items = $stmt->fetchAll();

        // Get supplier name
        if ($purchase->supplier_id) {
            $stmt2 = $this->db->prepare("SELECT name FROM suppliers WHERE id = :sid");
            $stmt2->execute(['sid' => $purchase->supplier_id]);
            $s = $stmt2->fetch();
            $purchase->supplier_name = $s ? $s->name : '-';
        } else {
            $purchase->supplier_name = '-';
        }

        return $purchase;
    }

    public function getRecent($limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, COALESCE(s.name, '-') as supplier_name
            FROM {$this->table} p
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            ORDER BY p.purchase_date DESC LIMIT :lim
        ");
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getMonthlyTotal()
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(total_amount), 0) as total
            FROM {$this->table}
            WHERE MONTH(purchase_date) = MONTH(CURDATE())
              AND YEAR(purchase_date) = YEAR(CURDATE())
        ");
        $stmt->execute();
        return (int) $stmt->fetch()->total;
    }

    public function getByDateRange($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, COALESCE(s.name, '-') as supplier_name
            FROM {$this->table} p
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            WHERE DATE(p.purchase_date) BETWEEN :from AND :to
            ORDER BY p.purchase_date DESC
        ");
        $stmt->execute(['from' => $from, 'to' => $to]);
        return $stmt->fetchAll();
    }
}
