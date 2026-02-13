<?php

class Product extends Model
{
    protected $table = 'products';

    public function findByBarcode($barcode)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE barcode = :barcode");
        $stmt->execute(['barcode' => $barcode]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function search($keyword)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE name LIKE :keyword 
            OR barcode LIKE :keyword 
            OR category LIKE :keyword
        ");
        $stmt->execute(['keyword' => "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateStock($id, $quantity, $operation = 'decrease')
    {
        $operator = $operation === 'decrease' ? '-' : '+';
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET stock = stock {$operator} :quantity 
            WHERE id = :id
        ");
        return $stmt->execute(['quantity' => $quantity, 'id' => $id]);
    }

    public function getLowStock($threshold = 10)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE stock <= :threshold");
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
