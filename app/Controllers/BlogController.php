<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogImage;
use App\Models\Course;
use App\Models\Subject;

class BlogController extends Controller
{
    public function index()
    {
        $postModel = new BlogPost();

        $courseSlug   = $_GET['course'] ?? null;
        $subjectSlug  = $_GET['subject'] ?? null;
        $search       = isset($_GET['q']) && trim($_GET['q']) !== '' ? trim($_GET['q']) : null;
        $page         = max(1, (int)($_GET['page'] ?? 1));
        $perPage      = 9;
        $offset       = ($page - 1) * $perPage;

        $courseModel = new Course();
        $subjectModel = new Subject();

        $courses = $courseModel->findAll();
        $currentCourse = null;
        $currentSubject = null;
        $subjectsForCourse = [];
        $courseId = null;
        $subjectId = null;

        if ($courseSlug) {
            $currentCourse = $courseModel->findBySlug($courseSlug);
            if ($currentCourse) {
                $courseId = (int)$currentCourse['id'];
                $subjectsForCourse = $subjectModel->findByCourse($courseId);

                if ($subjectSlug) {
                    $currentSubject = $subjectModel->findBySlugAndCourse($subjectSlug, $courseId);
                    if ($currentSubject) {
                        $subjectId = (int)$currentSubject['id'];
                    }
                }
            }
        }

        $posts      = $postModel->findPublished($courseId, $subjectId, $search, $perPage, $offset);
        $totalPosts = $postModel->countPublished($courseId, $subjectId, $search);
        $totalPages = max(1, (int)ceil($totalPosts / $perPage));

        $recentPosts = $postModel->findPublished(null, null, null, 5, 0);

        $this->view('blog/index', [
            'posts'             => $posts,
            'courses'           => $courses,
            'currentCourse'     => $currentCourse,
            'subjectsForCourse' => $subjectsForCourse,
            'currentSubject'    => $currentSubject,
            'recentPosts'       => $recentPosts,
            'search'            => $search,
            'page'              => $page,
            'totalPages'        => $totalPages,
            'totalPosts'        => $totalPosts,
        ]);
    }

    public function show($slug)
    {
        $postModel = new BlogPost();
        $post = $postModel->findBySlug($slug);

        if (!$post || $post['status'] !== 'published') {
            http_response_code(404);
            echo "Articolo non trovato";
            return;
        }
        $featuredImage = null;
        if (!empty($post['featured_image_id'])) {
            $imageModel = new BlogImage();
            $featuredImage = $imageModel->findById((int)$post['featured_image_id']);
        }

        $course = null;
        $subject = null;
        if (!empty($post['course_id'])) {
            $courseModel = new Course();
            $course = $courseModel->findById((int)$post['course_id']);
        }
        if (!empty($post['subject_id'])) {
            $subjectModel = new Subject();
            $subject = $subjectModel->findById((int)$post['subject_id']);
        }

        $relatedPosts = $postModel->findRelated(
            (int)$post['id'],
            isset($post['course_id']) ? (int)$post['course_id'] : null,
            isset($post['subject_id']) ? (int)$post['subject_id'] : null
        );

        $this->view('blog/show', [
            'post' => $post,
            'featuredImage' => $featuredImage,
            'course' => $course,
            'subject' => $subject,
            'relatedPosts' => $relatedPosts,
        ]);
    }

    // API per gestione blog in area admin
    public function apiIndex()
    {
        $this->requireAdmin();
        $postModel = new BlogPost();
        $posts = $postModel->getAllForAdmin();
        $this->jsonResponse(true, 'Blog posts retrieved', $posts);
    }

    public function apiStore()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $post = $this->parsePostPayload($data);

        $authorId = $this->currentUserId();
        if (!$authorId) {
            $this->jsonResponse(false, 'Autore non valido', [], 400);
        }

        $postModel = new BlogPost();
        $success = $postModel->createPost(
            $authorId,
            $post['title'],
            $post['excerpt'],
            $post['content'],
            $post['status'],
            $post['course_id'],
            $post['subject_id'],
            $post['seo_title'],
            $post['seo_description'],
            $post['seo_keywords'],
            $post['featured_image_id']
        );

        if ($success) {
            $this->jsonResponse(true, 'Articolo creato con successo');
        }

        $this->jsonResponse(false, 'Errore durante la creazione dell\'articolo', [], 500);
    }

    public function apiUpdate($id)
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $post = $this->parsePostPayload($data);

        $postModel = new BlogPost();
        $success = $postModel->updatePost(
            (int)$id,
            $post['title'],
            $post['excerpt'],
            $post['content'],
            $post['status'],
            $post['course_id'],
            $post['subject_id'],
            $post['seo_title'],
            $post['seo_description'],
            $post['seo_keywords'],
            $post['featured_image_id']
        );

        if ($success) {
            $this->jsonResponse(true, 'Articolo aggiornato con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'aggiornamento dell\'articolo', [], 500);
    }

    public function apiDelete($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $postModel = new BlogPost();
        $success = $postModel->delete((int)$id);

        if ($success) {
            $this->jsonResponse(true, 'Articolo eliminato con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'eliminazione dell\'articolo', [], 500);
    }

    // API per upload immagini del blog (area admin)
    public function apiImagesStore()
    {
        $this->requireAdmin();

        // Verifica CSRF da form multipart
        $this->verifyCsrfToken($_POST['csrf_token'] ?? '');

        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(false, 'File immagine non valido', [], 400);
        }

        $file = $_FILES['image'];

        // Controllo estensione
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $this->jsonResponse(false, 'Formato immagine non supportato', [], 400);
        }

        // Controllo dimensione (max ~5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->jsonResponse(false, 'Immagine troppo grande (max 5MB)', [], 400);
        }

        // Verifica che sia davvero un'immagine
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $this->jsonResponse(false, 'Il file caricato non è un\'immagine valida', [], 400);
        }

        $uploadDir = __DIR__ . '/../../public/uploads/blog_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $basename = bin2hex(random_bytes(16));
        $filename = $basename . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->jsonResponse(false, 'Errore durante il salvataggio del file', [], 500);
        }

        $relativePath = 'public/uploads/blog_images/' . $filename;
        $altText = isset($_POST['alt']) ? trim($_POST['alt']) : null;
        $postId = isset($_POST['post_id']) && $_POST['post_id'] !== '' ? (int)$_POST['post_id'] : null;

        $imageModel = new BlogImage();
        $imageId = $imageModel->createImage($postId, $relativePath, $altText);

        $imageUrl = BASE_URL . '/' . $relativePath;

        $this->jsonResponse(true, 'Immagine caricata con successo', [
            'id' => $imageId,
            'url' => $imageUrl,
            'path' => $relativePath,
        ]);
    }

    // API per corsi (Aree)
    public function apiCoursesIndex()
    {
        $this->requireAdmin();
        $courseModel = new Course();
        $withStats = isset($_GET['stats']);
        $courses = $withStats ? $courseModel->findAllWithStats() : $courseModel->findAllOrdered();
        $this->jsonResponse(true, 'Courses retrieved', $courses);
    }

    public function apiCoursesStore()
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->jsonResponse(false, 'Il nome dell\'area è obbligatorio', [], 400);
        }

        $courseModel = new Course();
        $success = $courseModel->createCourse($name);

        if ($success) {
            $this->jsonResponse(true, 'Area creata con successo', [
                'courses' => $courseModel->findAllWithStats(),
            ]);
        }

        $this->jsonResponse(false, 'Errore durante la creazione dell\'area', [], 500);
    }

    public function apiCoursesUpdate($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->jsonResponse(false, 'Il nome dell\'area è obbligatorio', [], 400);
        }

        $description = isset($data['description']) && trim($data['description']) !== '' ? trim($data['description']) : null;
        $sortOrder   = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;

        $courseModel = new Course();
        $success = $courseModel->updateCourse((int)$id, $name, $description, $sortOrder);

        if ($success) {
            $this->jsonResponse(true, 'Area aggiornata con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'aggiornamento', [], 500);
    }

    public function apiCoursesDelete($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $courseModel = new Course();
        $success = $courseModel->delete((int)$id);

        if ($success) {
            $this->jsonResponse(true, 'Area eliminata con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'eliminazione', [], 500);
    }

    // API per materie (Argomenti)
    public function apiSubjectsIndex()
    {
        $this->requireAdmin();
        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
        if ($courseId <= 0) {
            $this->jsonResponse(false, 'course_id mancante o non valido', [], 400);
        }

        $subjectModel = new Subject();
        $withStats = isset($_GET['stats']);
        $subjects = $withStats
            ? $subjectModel->findByCourseWithStats($courseId)
            : $subjectModel->findByCourse($courseId);
        $this->jsonResponse(true, 'Subjects retrieved', $subjects);
    }

    public function apiSubjectsStore()
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $courseId = isset($data['course_id']) ? (int)$data['course_id'] : 0;
        $name = trim($data['name'] ?? '');

        if ($courseId <= 0) {
            $this->jsonResponse(false, 'Nessuna area selezionata', [], 400);
        }
        if ($name === '') {
            $this->jsonResponse(false, 'Il nome dell\'argomento è obbligatorio', [], 400);
        }

        $subjectModel = new Subject();
        $success = $subjectModel->createSubject($courseId, $name);

        if ($success) {
            $this->jsonResponse(true, 'Argomento creato con successo', [
                'subjects' => $subjectModel->findByCourseWithStats($courseId),
            ]);
        }

        $this->jsonResponse(false, 'Errore durante la creazione dell\'argomento', [], 500);
    }

    public function apiSubjectsUpdate($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->jsonResponse(false, 'Il nome dell\'argomento è obbligatorio', [], 400);
        }

        $description = isset($data['description']) && trim($data['description']) !== '' ? trim($data['description']) : null;
        $sortOrder   = isset($data['sort_order']) ? (int)$data['sort_order'] : 0;

        $subjectModel = new Subject();
        $success = $subjectModel->updateSubject((int)$id, $name, $description, $sortOrder);

        if ($success) {
            $this->jsonResponse(true, 'Argomento aggiornato con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'aggiornamento', [], 500);
    }

    public function apiSubjectsDelete($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $subjectModel = new Subject();
        $success = $subjectModel->delete((int)$id);

        if ($success) {
            $this->jsonResponse(true, 'Argomento eliminato con successo');
        }

        $this->jsonResponse(false, 'Errore durante l\'eliminazione', [], 500);
    }

    /**
     * Valida e normalizza i campi di un articolo inviati dall'editor.
     * Risponde 400 se titolo o contenuto mancano.
     */
    private function parsePostPayload(array $data): array
    {
        $optionalText = fn(string $key) => isset($data[$key]) ? trim((string) $data[$key]) : null;
        $optionalId   = fn(string $key) => isset($data[$key]) && $data[$key] !== '' ? (int) $data[$key] : null;

        $post = [
            'title'             => trim((string) ($data['title'] ?? '')),
            'excerpt'           => $optionalText('excerpt'),
            'content'           => trim((string) ($data['content'] ?? '')),
            'status'            => in_array($data['status'] ?? '', ['draft', 'published'], true) ? $data['status'] : 'draft',
            'seo_title'         => $optionalText('seo_title'),
            'seo_description'   => $optionalText('seo_description'),
            'seo_keywords'      => $optionalText('seo_keywords'),
            'featured_image_id' => $optionalId('featured_image_id'),
            'course_id'         => $optionalId('course_id'),
            'subject_id'        => $optionalId('subject_id'),
        ];

        if ($post['title'] === '' || $post['content'] === '') {
            $this->jsonResponse(false, 'Titolo e contenuto sono obbligatori', [], 400);
        }

        return $post;
    }
}
