<?php

namespace App\Models;

class LessonPackage extends Model
{
    protected $table = 'lesson_packages';

    /**
     * Restituisce la somma totale dei prezzi di tutti i pacchetti acquistati dallo studente (pagati e non pagati).
     */
    public function sumAllPrices(int $studentId = 0): float
    {
        if ($studentId > 0) {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table} WHERE student_id = ?");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $this->db->query("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table}");
        }
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function getForStudent(int $studentId): array
    {
        $sql = "SELECT p.*,
                COALESCE((SELECT SUM(duration) FROM lessons WHERE package_id = p.id AND status IN ('scheduled', 'completed')), 0) AS used_minutes,
                COALESCE((SELECT SUM(amount) FROM payments WHERE package_id = p.id), 0) AS paid_amount
                FROM {$this->table} p 
                WHERE p.student_id = ? 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT p.*, u.name AS student_name,
                COALESCE((SELECT SUM(duration) FROM lessons WHERE package_id = p.id AND status IN ('scheduled', 'completed')), 0) AS used_minutes,
                COALESCE((SELECT SUM(amount) FROM payments WHERE package_id = p.id), 0) AS paid_amount
                FROM {$this->table} p 
                JOIN users u ON p.student_id = u.id 
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createPackage(int $studentId, int $totalMinutes = 600, ?string $description = null, string $status = 'active', ?string $createdAt = null, ?float $price = null, bool $isPaid = false): bool
    {
        if ($createdAt === null) {
            $createdAt = date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, total_minutes, description, status, created_at, price, is_paid) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$studentId, $totalMinutes, $description, $status, $createdAt, $price, $isPaid ? 1 : 0]);
    }

    public function getAggregatedAll(): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_packages,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_packages,
                    SUM(total_minutes) AS total_minutes,
                    (SELECT COALESCE(SUM(duration), 0) FROM lessons WHERE package_id IS NOT NULL AND status = 'completed') AS used_minutes
                FROM {$this->table}";

        $stmt = $this->db->query($sql);
        $row = $stmt->fetch();

        return [
            'total_packages' => (int) ($row['total_packages'] ?? 0),
            'active_packages' => (int) ($row['active_packages'] ?? 0),
            'total_minutes' => (int) ($row['total_minutes'] ?? 0),
            'used_minutes' => (int) ($row['used_minutes'] ?? 0),
        ];
    }

    public function getAggregatedForStudent(int $studentId): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_packages,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_packages,
                    SUM(total_minutes) AS total_minutes,
                    (SELECT COALESCE(SUM(l.duration), 0) FROM lessons l JOIN {$this->table} p2 ON l.package_id = p2.id WHERE p2.student_id = ? AND l.status = 'completed') AS used_minutes
                FROM {$this->table}
                WHERE student_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId, $studentId]);
        $row = $stmt->fetch();

        return [
            'total_packages' => (int) ($row['total_packages'] ?? 0),
            'active_packages' => (int) ($row['active_packages'] ?? 0),
            'total_minutes' => (int) ($row['total_minutes'] ?? 0),
            'used_minutes' => (int) ($row['used_minutes'] ?? 0),
        ];
    }

    /**
     * Importo ancora da incassare sui pacchetti non pagati (prezzo meno versamenti già collegati al pacchetto).
     * I pacchetti annullati non vengono considerati.
     */
    public function sumUnpaid(int $studentId = 0): float
    {
        $sql = "SELECT COALESCE(SUM(GREATEST(p.price - COALESCE(pay.paid, 0), 0)), 0) AS total
                FROM {$this->table} p
                LEFT JOIN (SELECT package_id, SUM(amount) AS paid FROM payments WHERE package_id IS NOT NULL GROUP BY package_id) pay
                    ON pay.package_id = p.id
                WHERE p.is_paid = 0 AND p.status <> 'cancelled' AND p.price IS NOT NULL";
        if ($studentId > 0) {
            $stmt = $this->db->prepare($sql . " AND p.student_id = ?");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function markPaid(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_paid = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
