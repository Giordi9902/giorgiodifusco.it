<?php

namespace App\Models;

class Subject extends Model
{
    protected $table = 'subjects';

    public function findBySlugAndCourse(string $slug, int $courseId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ? AND course_id = ? LIMIT 1");
        $stmt->execute([$slug, $courseId]);
        return $stmt->fetch();
    }

    public function findByCourse(int $courseId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY sort_order ASC, name ASC"
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function findByCourseWithStats(int $courseId): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, COUNT(bp.id) AS post_count
             FROM {$this->table} s
             LEFT JOIN blog_posts bp ON bp.subject_id = s.id
             WHERE s.course_id = ?
             GROUP BY s.id
             ORDER BY s.sort_order ASC, s.name ASC"
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public function updateSubject(int $id, string $name, ?string $description, int $sortOrder): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET name = ?, description = ?, sort_order = ? WHERE id = ?"
        );
        return $stmt->execute([trim($name), $description, $sortOrder, $id]);
    }

    public function createSubject(int $courseId, string $name): bool
    {
        $name = trim($name);
        if ($courseId <= 0 || $name === '') {
            return false;
        }

        $slug = $this->generateUniqueSlug($courseId, $name);

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (course_id, name, slug) VALUES (?, ?, ?)");
        return $stmt->execute([$courseId, $name, $slug]);
    }

    private function generateUniqueSlug(int $courseId, string $name): string
    {
        $base = $this->slugify($name);
        if ($base === '') {
            $base = 'materia';
        }

        $slug = $base;
        $i = 2;

        while ($this->slugExists($courseId, $slug)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function slugExists(int $courseId, string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE course_id = ? AND slug = ? LIMIT 1");
        $stmt->execute([$courseId, $slug]);
        return (bool)$stmt->fetch();
    }
}
