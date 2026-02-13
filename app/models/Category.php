<?php

class Category extends Model
{
    protected $table = 'categories';

    public function nameExists($name, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as c FROM {$this->table} WHERE name = :n";
        $params = ['n' => $name];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->c > 0;
    }

    public function getWithProductCount()
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON p.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll();
    }

    public function search($keyword)
    {
        $like = "%{$keyword}%";
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count
            FROM {$this->table} c
            LEFT JOIN products p ON p.category_id = c.id
            WHERE c.name LIKE :kw1 OR c.description LIKE :kw2
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        $stmt->execute(['kw1' => $like, 'kw2' => $like]);
        return $stmt->fetchAll();
    }
}
