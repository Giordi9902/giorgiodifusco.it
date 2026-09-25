<?php

namespace App\Models;

class BlogImage extends Model
{
    protected $table = 'blog_images';

    public function createImage(?int $postId, string $path, ?string $altText = null): int
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (post_id, path, alt_text) VALUES (?, ?, ?)");
        $stmt->execute([$postId, $path, $altText]);

        return (int)$this->db->lastInsertId();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row ?: null;
    }
}
