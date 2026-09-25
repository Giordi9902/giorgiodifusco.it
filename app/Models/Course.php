<?php

namespace App\Models;

class Course extends Model
{
    protected $table = 'courses';

    public function findBySlug(string $slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function createCourse(string $name): bool
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        $slug = $this->generateUniqueSlug($name);

        $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, slug) VALUES (?, ?)");
        return $stmt->execute([$name, $slug]);
    }

    public function findAllOrdered(): array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY sort_order ASC, name ASC");
        return $stmt->fetchAll();
    }

    public function findAllWithStats(): array
    {
        $sql = "SELECT c.*,
                    COUNT(DISTINCT s.id)  AS subject_count,
                    COUNT(DISTINCT bp.id) AS post_count
                FROM {$this->table} c
                LEFT JOIN subjects s    ON s.course_id  = c.id
                LEFT JOIN blog_posts bp ON bp.course_id = c.id
                GROUP BY c.id
                ORDER BY c.sort_order ASC, c.name ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function updateCourse(int $id, string $name, ?string $description, int $sortOrder): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET name = ?, description = ?, sort_order = ? WHERE id = ?"
        );
        return $stmt->execute([trim($name), $description, $sortOrder, $id]);
    }

    private function generateUniqueSlug(string $name): string
    {
        $base = $this->slugify($name);
        if ($base === '') {
            $base = 'corso';
        }

        $slug = $base;
        $i = 2;

        while ($this->slugExists($slug)) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return (bool)$stmt->fetch();
    }
}
