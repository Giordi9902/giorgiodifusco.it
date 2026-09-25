<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\LessonPackage;
use App\Models\Lesson;

class PackageController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $packageModel = new LessonPackage();
        $lessonModel = new Lesson();

        if ($this->isAdmin()) {
            $packages = $packageModel->getAllForAdmin();
        } else {
            $studentId = $this->currentUserId();
            $packages = $packageModel->getForStudent($studentId);
        }

        // Fetch lessons for each package
        foreach ($packages as &$package) {
            $package['lessons'] = $lessonModel->getForPackage($package['id']);
        }

        $this->jsonResponse(true, 'Packages retrieved', $packages);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;
        $totalMinutes = isset($data['total_minutes']) ? (int) $data['total_minutes'] : 600;
        $description = trim($data['description'] ?? '');
        $status = in_array(($data['status'] ?? ''), ['active', 'completed', 'cancelled']) ? $data['status'] : 'active';
        $createdAt = !empty($data['created_at']) ? trim($data['created_at']) : null;
        $price = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null;
        $isPaid = !empty($data['is_paid']);

        if (!$studentId || !$totalMinutes) {
            $this->jsonResponse(false, 'Dati pacchetto mancanti o non validi', [], 400);
        }

        $packageModel = new LessonPackage();
        $success = $packageModel->createPackage($studentId, $totalMinutes, $description, $status, $createdAt, $price, $isPaid);

        if ($success) {
            $this->jsonResponse(true, "Package created successfully", [], 201);
        }

        $this->jsonResponse(false, 'Errore durante la creazione del pacchetto', [], 500);
    }

    public function show($id)
    {
        $this->requireAuth();

        $packageModel = new LessonPackage();
        $lessonModel = new Lesson();

        $id = (int) $id;
        $package = $packageModel->findById($id);

        if (!$package) {
            $this->jsonResponse(false, 'Pacchetto non trovato', [], 404);
        }

        if (!$this->isAdmin() && $this->currentUserId() !== (int) $package['student_id']) {
            $this->jsonResponse(false, 'Forbidden', [], 403);
        }

        $lessons = $lessonModel->getForPackage($id);

        $this->jsonResponse(true, 'Package details', [
            'package' => $package,
            'lessons' => $lessons,
        ]);
    }
}
