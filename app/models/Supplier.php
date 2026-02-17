<?php

class Supplier extends Model
{
    protected $table = 'suppliers';

    public function search($keyword)
    {
        $like = "%{$keyword}%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE name LIKE :kw1 OR phone LIKE :kw2 OR email LIKE :kw3
            ORDER BY name ASC LIMIT 50
        ");
        $stmt->execute(['kw1' => $like, 'kw2' => $like, 'kw3' => $like]);
        return $stmt->fetchAll();
    }

    public function getWithPurchaseCount()
    {
        $stmt = $this->db->query("
            SELECT s.*, COUNT(p.id) as purchase_count,
                   COALESCE(SUM(p.total_amount), 0) as total_purchased
            FROM {$this->table} s
            LEFT JOIN purchases p ON p.supplier_id = s.id
            GROUP BY s.id
            ORDER BY s.name ASC
        ");
        return $stmt->fetchAll();
    }
}
