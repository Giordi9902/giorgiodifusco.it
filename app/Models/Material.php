<?php

namespace App\Models;

class Material extends Model
{
    protected $table = 'materials';

    public function getForStudent(int $studentId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function getAllForAdmin(): array
    {
        $sql = "SELECT m.*, u.name AS student_name FROM {$this->table} m JOIN users u ON m.student_id = u.id ORDER BY m.uploaded_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createMaterial(int $studentId, string $filename, string $filepath): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, type, title, filename, filepath) VALUES (?, 'file', ?, ?, ?)"
        );
        return $stmt->execute([$studentId, $filename, $filename, $filepath]);
    }

    public function createLink(int $studentId, string $title, string $url): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, type, title, url) VALUES (?, 'link', ?, ?)"
        );
        return $stmt->execute([$studentId, $title, $url]);
    }

    public function createText(int $studentId, string $title, string $content): bool
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_id, type, title, content) VALUES (?, 'text', ?, ?)"
        );
        return $stmt->execute([$studentId, $title, $content]);
    }
}
