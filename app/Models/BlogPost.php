<?php

namespace App\Models;

class BlogPost extends Model
{
    protected $table = 'blog_posts';

    public function findPublished(
        ?int $courseId = null,
        ?int $subjectId = null,
        ?string $search = null,
        int $limit = 12,
        int $offset = 0
    ): array {
        $conditions = ["bp.status = 'published'"];
        $params = [];

        if ($subjectId !== null) {
            $conditions[] = 'bp.subject_id = ?';
            $params[] = $subjectId;
        } elseif ($courseId !== null) {
            $conditions[] = 'bp.course_id = ?';
            $params[] = $courseId;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = '(bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)';
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT bp.*, bi.path AS featured_image_path, bi.alt_text AS featured_image_alt
                FROM {$this->table} bp
                LEFT JOIN blog_images bi ON bp.featured_image_id = bi.id
                WHERE {$where}
                ORDER BY (bp.published_at IS NULL), bp.published_at DESC, bp.created_at DESC
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countPublished(
        ?int $courseId = null,
        ?int $subjectId = null,
        ?string $search = null
    ): int {
        $conditions = ["status = 'published'"];
        $params = [];

        if ($subjectId !== null) {
            $conditions[] = 'subject_id = ?';
            $params[] = $subjectId;
        } elseif ($courseId !== null) {
            $conditions[] = 'course_id = ?';
            $params[] = $courseId;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = '(title LIKE ? OR excerpt LIKE ? OR content LIKE ?)';
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findAllPublishedForSitemap(): array
    {
        return $this->db->query(
            "SELECT slug, published_at, updated_at, created_at
             FROM {$this->table}
             WHERE status = 'published'
             ORDER BY (published_at IS NULL), published_at DESC, created_at DESC"
        )->fetchAll();
    }

    public function findBySlug(string $slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }

    public function getAllForAdmin()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function createPost(
        int $authorId,
        string $title,
        ?string $excerpt,
        string $content,
        string $status = 'draft',
        ?int $courseId = null,
        ?int $subjectId = null,
        ?string $seoTitle = null,
        ?string $seoDescription = null,
        ?string $seoKeywords = null,
        ?int $featuredImageId = null
    ) {
        $slug = $this->generateUniqueSlug($title);
        $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (author_id, course_id, subject_id, title, slug, excerpt, content, seo_title, seo_description, seo_keywords, featured_image_id, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $authorId, $courseId, $subjectId, $title, $slug, $excerpt, $content,
            $seoTitle, $seoDescription, $seoKeywords, $featuredImageId, $status, $publishedAt,
        ]);
    }

    public function updatePost(
        int $id,
        string $title,
        ?string $excerpt,
        string $content,
        string $status,
        ?int $courseId = null,
        ?int $subjectId = null,
        ?string $seoTitle = null,
        ?string $seoDescription = null,
        ?string $seoKeywords = null,
        ?int $featuredImageId = null
    ) {
        $stmt = $this->db->prepare("SELECT status, published_at FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        $current = $stmt->fetch();
        if (!$current) {
            return false;
        }

        $publishedAt = $current['published_at'];
        if ($status === 'published' && $current['status'] !== 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET title = ?, excerpt = ?, content = ?, seo_title = ?, seo_description = ?,
                 seo_keywords = ?, featured_image_id = ?, status = ?, published_at = ?,
                 course_id = ?, subject_id = ?, updated_at = NOW()
             WHERE id = ?"
        );

        return $stmt->execute([
            $title, $excerpt, $content, $seoTitle, $seoDescription,
            $seoKeywords, $featuredImageId, $status, $publishedAt,
            $courseId, $subjectId, $id,
        ]);
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status = ?");
        $stmt->execute([$status]);
        return (int)$stmt->fetchColumn();
    }

    public function findRecent(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, title, slug, status, published_at, created_at
             FROM {$this->table} ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function findRelated(int $postId, ?int $courseId = null, ?int $subjectId = null, int $limit = 4): array
    {
        $conditions = ["status = 'published'", 'id <> ?'];
        $params = [$postId];

        if ($subjectId !== null) {
            $conditions[] = 'subject_id = ?';
            $params[] = $subjectId;
        } elseif ($courseId !== null) {
            $conditions[] = 'course_id = ?';
            $params[] = $courseId;
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT * FROM {$this->table} WHERE {$where}
                ORDER BY (published_at IS NULL), published_at DESC, created_at DESC
                LIMIT " . (int)$limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function generateUniqueSlug(string $title): string
    {
        $base = $this->slugify($title);
        if ($base === '') {
            $base = 'articolo';
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
