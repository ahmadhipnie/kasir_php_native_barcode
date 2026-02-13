<?php

class User extends Model
{
    protected $table = 'users';

    public function findByUsername($username)
    {
        return $this->findWhere('username', $username);
    }

    public function authenticate($username, $password)
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    public function usernameExists($username, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as c FROM {$this->table} WHERE username = :u";
        $params = ['u' => $username];
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()->c > 0;
    }

    public function search($keyword)
    {
        $like = "%{$keyword}%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE name LIKE :kw1 OR username LIKE :kw2 OR email LIKE :kw3
            ORDER BY name ASC LIMIT 50
        ");
        $stmt->execute(['kw1' => $like, 'kw2' => $like, 'kw3' => $like]);
        return $stmt->fetchAll();
    }
}
