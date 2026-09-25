<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Report;
use App\Models\Lesson;

class ReportController extends Controller
{
    /**
     * Restituisce il report associato a una lezione.
     * Admin sempre, studente solo se la lezione è sua.
     */
    public function index($lessonId)
    {
        $this->requireAuth();

        $lessonId = (int) $lessonId;
        if ($lessonId <= 0) {
            $this->jsonResponse(false, 'ID lezione non valido', [], 400);
        }

        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($lessonId);

        if (!$lesson) {
            $this->jsonResponse(false, 'Lezione non trovata', [], 404);
        }

        if (!$this->isAdmin() && $this->currentUserId() !== (int) $lesson['student_id']) {
            $this->jsonResponse(false, 'Forbidden', [], 403);
        }

        $reportModel = new Report();
        $report = $reportModel->getByLesson($lessonId);

        $this->jsonResponse(true, 'Report retrieved', $report ?: null);
    }

    /**
     * Crea o aggiorna il report di una lezione (solo admin).
     */
    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $lessonId = isset($data['lesson_id']) ? (int) $data['lesson_id'] : 0;
        $reportText = trim($data['report_text'] ?? '');
        $homework = trim($data['homework'] ?? '');

        if (!$lessonId || ($reportText === '' && $homework === '')) {
            $this->jsonResponse(false, 'Dati report mancanti o non validi', [], 400);
        }

        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($lessonId);
        if (!$lesson) {
            $this->jsonResponse(false, 'Lezione non trovata', [], 404);
        }

        $reportModel = new Report();
        $success = $reportModel->upsert($lessonId, $reportText, $homework);

        if ($success) {
            $this->jsonResponse(true, 'Report salvato correttamente', [], 201);
        }

        $this->jsonResponse(false, 'Errore durante il salvataggio del report', [], 500);
    }

    /**
     * Elimina il report associato a una lezione (solo admin).
     */
    public function delete($lessonId)
    {
        $this->requireAdmin();

        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $lessonId = (int) $lessonId;
        if ($lessonId <= 0) {
            $this->jsonResponse(false, 'ID lezione non valido', [], 400);
        }

        $lessonModel = new Lesson();
        $lesson = $lessonModel->findById($lessonId);
        if (!$lesson) {
            $this->jsonResponse(false, 'Lezione non trovata', [], 404);
        }

        $reportModel = new Report();
        $reportModel->deleteByLesson($lessonId);

        $this->jsonResponse(true, 'Report rimosso correttamente');
    }
}
