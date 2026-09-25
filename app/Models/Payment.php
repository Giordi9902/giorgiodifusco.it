<?php

namespace App\Models;

class Payment extends Model
{
    protected $table = 'payments';

    public function getAllForAdmin(): array
    {
        $sql = "SELECT p.*, u.name AS student_name, lp.description AS package_description FROM {$this->table} p JOIN users u ON p.student_id = u.id LEFT JOIN lesson_packages lp ON p.package_id = lp.id ORDER BY p.date DESC, p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getForStudent(int $studentId): array
    {
        $stmt = $this->db->prepare("SELECT p.*, lp.description AS package_description FROM {$this->table} p LEFT JOIN lesson_packages lp ON p.package_id = lp.id WHERE p.student_id = ? ORDER BY p.date DESC, p.created_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function createPayment(int $studentId, float $amount, string $date, ?string $method = null, ?string $notes = null, ?int $packageId = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, amount, date, method, notes, package_id) VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$studentId, $amount, $date, $method, $notes, $packageId]);
    }

    public function sumAll(): float
    {
        $stmt = $this->db->query("SELECT SUM(amount) AS total FROM {$this->table}");
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0.0);
    }

    public function sumForStudent(int $studentId): float
    {
        $stmt = $this->db->prepare("SELECT SUM(amount) AS total FROM {$this->table} WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0.0);
    }

    public function sumForPackage(int $packageId): float
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM {$this->table} WHERE package_id = ?");
        $stmt->execute([$packageId]);
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0.0);
    }
}
