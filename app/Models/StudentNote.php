<?php

namespace App\Models;

class StudentNote extends Model
{
    protected $table = 'student_notes';

    public function getForStudent($studentId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ? ORDER BY created_at DESC");
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function createNote($studentId, $content)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (student_id, content) VALUES (?, ?)");
        return $stmt->execute([$studentId, $content]);
    }

    public function updateNote($id, $content)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET content = ? WHERE id = ?");
        return $stmt->execute([$content, $id]);
    }
}
