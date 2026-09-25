<?php

namespace App\Models;

class Report extends Model
{
    protected $table = 'reports';

    public function getByLesson(int $lessonId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE lesson_id = ?");
        $stmt->execute([$lessonId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Crea o aggiorna il report associato a una lezione.
     */
    public function upsert(int $lessonId, string $reportText, string $homework): bool
    {
        $existing = $this->getByLesson($lessonId);

        if ($existing) {
            $stmt = $this->db->prepare(
                "UPDATE {$this->table} SET report_text = ?, homework = ? WHERE lesson_id = ?"
            );
            return $stmt->execute([$reportText, $homework, $lessonId]);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (lesson_id, report_text, homework) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$lessonId, $reportText, $homework]);
    }

    /**
     * Elimina il report associato a una lezione.
     */
    public function deleteByLesson(int $lessonId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE lesson_id = ?");
        return $stmt->execute([$lessonId]);
    }
}
