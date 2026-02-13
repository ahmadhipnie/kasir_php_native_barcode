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

            foreach ($items as $item) {
                $itemData = [
                    'transaction_id' => $transactionId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ];

                $stmt = $this->db->prepare("
                    INSERT INTO transaction_items (transaction_id, product_id, quantity, price, subtotal) 
                    VALUES (:transaction_id, :product_id, :quantity, :price, :subtotal)
                ");
                $stmt->execute($itemData);

                $productModel = new Product();
                $productModel->updateStock($item['product_id'], $item['quantity'], 'decrease');
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
        
        if ($transaction) {
            $stmt = $this->db->prepare("
                SELECT ti.*, p.name as product_name, p.barcode 
                FROM transaction_items ti
                JOIN products p ON ti.product_id = p.id
                WHERE ti.transaction_id = :transaction_id
            ");
            $stmt->execute(['transaction_id' => $id]);
            $transaction->items = $stmt->fetchAll(PDO::FETCH_OBJ);
        }

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
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    public function getTodayCount()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE DATE(transaction_date) = CURDATE()
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }

    public function getRecent($limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            ORDER BY transaction_date DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getSalesByDateRange($startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE DATE(transaction_date) BETWEEN :start_date AND :end_date
            ORDER BY transaction_date DESC
        ");
        $stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
