<?php

namespace App\Models;

class User extends Model
{
    protected $table = 'users';

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function create($name, $email, $password, $role = 'STUDENT')
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, email, password, role) VALUES (?, ?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $stmt->execute([$name, $email, $hashedPassword, $role]);
    }

    public function update($id, $name, $email)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function getStudents()
    {
        $stmt = $this->db->query("SELECT id, name, email, created_at FROM {$this->table} WHERE role = 'STUDENT' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function countStudents(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM {$this->table} WHERE role = 'STUDENT'");
        $row = $stmt->fetch();
        return (int)($row['total'] ?? 0);
    }
}
