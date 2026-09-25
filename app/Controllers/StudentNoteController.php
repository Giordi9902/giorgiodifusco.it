<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\StudentNote;

class StudentNoteController extends Controller
{
    public function index($studentId)
    {
        $this->requireAuth();

        if (!$this->isAdmin() && $this->currentUserId() !== (int) $studentId) {
            $this->jsonResponse(false, 'Forbidden', [], 403);
        }

        $noteModel = new StudentNote();
        $notes = $noteModel->getForStudent((int) $studentId);

        $this->jsonResponse(true, 'Notes retrieved', $notes);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;
        $content = trim($data['content'] ?? '');

        if (!$studentId || $content === '') {
            $this->jsonResponse(false, 'Dati mancanti', [], 400);
        }

        $noteModel = new StudentNote();
        $success = $noteModel->createNote($studentId, $content);

        if ($success) {
            $this->jsonResponse(true, "Note added successfully", [], 201);
        }

        $this->jsonResponse(false, 'Errore durante il salvataggio della nota', [], 500);
    }
}
