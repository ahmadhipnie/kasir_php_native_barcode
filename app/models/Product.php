<?php

class Product extends Model
{
    protected $table = 'products';

    public function findByBarcode($barcode)
    {
        return $this->findWhere('barcode', $barcode);
    }

    public function search($keyword)
    {
        $like = "%{$keyword}%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE name LIKE :kw1 OR barcode LIKE :kw2 OR category LIKE :kw3
            ORDER BY name ASC LIMIT 20
        ");
        $stmt->execute(['kw1' => $like, 'kw2' => $like, 'kw3' => $like]);
        return $stmt->fetchAll();
    }

    public function updateStock($id, $quantity, $operation = 'decrease')
    {
        $op = $operation === 'decrease' ? '-' : '+';
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET stock = stock {$op} :qty WHERE id = :id AND stock {$op} :qty2 >= 0
        ");
        $stmt->execute(['qty' => $quantity, 'id' => $id, 'qty2' => $quantity]);
        return $stmt->rowCount() > 0;
    }

    public function getLowStock($threshold = 10)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} WHERE stock <= :t ORDER BY stock ASC
        ");
        $stmt->execute(['t' => $threshold]);
        return $stmt->fetchAll();
    }

    public function countAll()
    {
        return $this->count();
    }

    public function getCategories()
    {
        $stmt = $this->db->query("SELECT DISTINCT category FROM {$this->table} WHERE category != '' ORDER BY category");
        return $stmt->fetchAll();
    }

    public function barcodeExists($barcode, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as c FROM {$this->table} WHERE barcode = :b";
        $params = ['b' => $barcode];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->c > 0;
    }
}
