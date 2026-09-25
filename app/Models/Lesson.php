<?php

namespace App\Models;

class Lesson extends Model
{
    protected $table = 'lessons';

    /**
     * Aggiorna tutte le lezioni "scheduled" già scadute (data < ora attuale) a "completed" per uno studente.
     */
    public function updateExpiredLessonsToCompleted(int $studentId): void
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = 'completed' WHERE student_id = ? AND status = 'scheduled' AND date < NOW()");
        $stmt->execute([$studentId]);
    }

    public function sumCompletedMinutesForStudent(int $studentId): int
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(duration), 0) AS total_minutes FROM {$this->table} WHERE student_id = ? AND status = 'completed'");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return (int) ($row['total_minutes'] ?? 0);
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT l.*, u.name AS student_name FROM {$this->table} l JOIN users u ON l.student_id = u.id ORDER BY l.date DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getForStudent(int $studentId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ? ORDER BY date DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getForPackage(int $packageId): array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE package_id = ?
                ORDER BY date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }

    public function createLesson(int $studentId, string $dateTime, int $duration, string $topic, string $lessonType = 'single', ?int $packageId = null, string $status = 'scheduled', ?float $price = null, bool $isPaid = false, string $locationType = 'in_person', ?string $meetingLink = null): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, date, duration, lesson_type, package_id, topic, status, price, is_paid, location_type, meeting_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([$studentId, $dateTime, $duration, $lessonType, $packageId, $topic, $status, $price, $isPaid ? 1 : 0, $locationType, $meetingLink]);
    }

    public function updateLesson(int $id, string $dateTime, int $duration, string $topic, string $status, ?float $price = null, bool $isPaid = false, string $locationType = 'in_person', ?string $meetingLink = null): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET date = ?, duration = ?, topic = ?, status = ?, price = ?, is_paid = ?, location_type = ?, meeting_link = ? WHERE id = ?"
        );
        return $stmt->execute([$dateTime, $duration, $topic, $status, $price, $isPaid ? 1 : 0, $locationType, $meetingLink, $id]);
    }

    public function countAll(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) AS total FROM {$this->table}");
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function countForStudent(int $studentId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function countForStudentByStatus(int $studentId, string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM {$this->table} WHERE student_id = ? AND status = ?");
        $stmt->execute([$studentId, $status]);
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    public function getNextLessonForStudent(int $studentId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ? AND status = 'scheduled' AND date >= NOW() ORDER BY date ASC LIMIT 1");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function sumAllSingleLessonPrices(int $studentId = 0): float
    {
        if ($studentId > 0) {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table} WHERE student_id = ? AND lesson_type = 'single'");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $this->db->query("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table} WHERE lesson_type = 'single'");
        }
        $row = $stmt->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public function sumUnpaidSingleLessons(int $studentId = 0): float
    {
        if ($studentId > 0) {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table} WHERE student_id = ? AND lesson_type = 'single' AND is_paid = 0 AND status <> 'cancelled'");
            $stmt->execute([$studentId]);
        } else {
            $stmt = $this->db->query("SELECT COALESCE(SUM(price), 0) AS total FROM {$this->table} WHERE lesson_type = 'single' AND is_paid = 0 AND status <> 'cancelled'");
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
