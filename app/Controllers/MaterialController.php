<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Material;

class MaterialController extends Controller
{
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'doc', 'docx', 'odt', 'txt', 'rtf',
        'ppt', 'pptx', 'odp', 'xls', 'xlsx', 'ods', 'csv',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'zip',
    ];

    public function index()
    {
        $this->requireAuth();

        $materialModel = new Material();

        if ($this->isAdmin()) {
            $materials = $materialModel->getAllForAdmin();
        } else {
            $studentId = $this->currentUserId();
            $materials = $materialModel->getForStudent($studentId);
        }

        $this->jsonResponse(true, 'Materials retrieved', $materials);
    }

    public function store()
    {
        $this->requireAdmin();

        // JSON per link/testo, multipart per i file
        $data = $this->input();
        $type = $data['type'] ?? 'text';
        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        if (!$studentId) {
            $this->jsonResponse(false, 'Studente non specificato.', [], 400);
        }

        $title = trim($data['title'] ?? 'Senza Titolo');
        $materialModel = new Material();

        if ($type === 'file') {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                $this->jsonResponse(false, 'Errore nel caricamento del file.', [], 400);
            }

            $originalName = basename($_FILES['file']['name']);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                $this->jsonResponse(false, 'Tipo di file non consentito.', [], 400);
            }

            $uploadDir = __DIR__ . '/../../public/uploads/materials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            // Nome file sicuro: solo caratteri innocui, prefisso casuale non indovinabile
            $safeName = preg_replace('/[^A-Za-z0-9._-]+/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $uniqueFilename = bin2hex(random_bytes(8)) . '_' . $safeName . '.' . $ext;
            $targetPath = $uploadDir . $uniqueFilename;
            $publicPath = '/uploads/materials/' . $uniqueFilename;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                $success = $materialModel->createMaterial($studentId, $title, $publicPath);
                if ($success) {
                    $this->jsonResponse(true, "File caricato con successo.", [], 201);
                }
            }
            $this->jsonResponse(false, 'Errore durante il salvataggio del file.', [], 500);

        } elseif ($type === 'link') {
            $url = trim($data['url'] ?? '');
            if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
                $this->jsonResponse(false, 'URL non valido.', [], 400);
            }
            $success = $materialModel->createLink($studentId, $title, $url);
            if ($success) {
                $this->jsonResponse(true, "Link salvato con successo.", [], 201);
            }

        } elseif ($type === 'text') {
            $content = trim($data['content'] ?? '');
            if ($content === '') {
                $this->jsonResponse(false, 'Contenuto testo vuoto.', [], 400);
            }
            $success = $materialModel->createText($studentId, $title, $content);
            if ($success) {
                $this->jsonResponse(true, "Materiale testuale salvato con successo.", [], 201);
            }
        }

        $this->jsonResponse(false, 'Tipo di materiale non valido o errore generale.', [], 400);
    }
}
