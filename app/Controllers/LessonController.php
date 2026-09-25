<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Lesson;
use App\Models\LessonPackage;
use DateTimeImmutable;

class LessonController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $lessonModel = new Lesson();

        if ($this->isAdmin()) {
            $lessons = $lessonModel->getAllForAdmin();
        } else {
            $studentId = $this->currentUserId();
            $lessons = $lessonModel->getForStudent($studentId);
        }

        $this->jsonResponse(true, "Lessons retrieved", $lessons);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;
        $dateTime = trim($data['date'] ?? '');
        $duration = isset($data['duration']) ? (int) $data['duration'] : 0;
        $topic = trim($data['topic'] ?? '');
        $lessonType = $data['lesson_type'] ?? 'single';
        $packageId = isset($data['package_id']) && $data['package_id'] !== '' ? (int) $data['package_id'] : null;
        $price = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null;
        $isPaid = !empty($data['is_paid']);
        $locationType = $data['location_type'] ?? 'in_person';
        $meetingLink = trim($data['meeting_link'] ?? '');

        if (!$studentId || $dateTime === '' || !$duration || $topic === '') {
            $this->jsonResponse(false, 'Dati lezione mancanti o non validi', [], 400);
        }

        try {
            $lessonDate = new DateTimeImmutable($dateTime);
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Data lezione non valida', [], 400);
        }

        $now = new DateTimeImmutable('now');
        $status = $lessonDate > $now ? 'scheduled' : 'completed';

        if ($lessonType !== 'single' && $lessonType !== 'package') {
            $lessonType = 'single';
        }

        if ($locationType !== 'online' && $locationType !== 'in_person') {
            $locationType = 'in_person';
        }

        if ($locationType === 'online' && $meetingLink === '') {
            $this->jsonResponse(false, 'Per una lezione online è necessario specificare un link di accesso', [], 400);
        }

        if ($locationType === 'in_person') {
            $meetingLink = null;
        }

        // If part of a package, inherit its payment status
        if ($lessonType === 'package' && $packageId) {
            $packageModel = new LessonPackage();
            $package = $packageModel->findById($packageId);
            if ($package) {
                $isPaid = (bool) $package['is_paid'];
            }
        }

        $lessonModel = new Lesson();
        $success = $lessonModel->createLesson($studentId, $dateTime, $duration, $topic, $lessonType, $packageId, $status, $price, $isPaid, $locationType, $meetingLink);

        if ($success) {
            $this->jsonResponse(true, "Lesson scheduled successfully", [], 201);
        }

        $this->jsonResponse(false, 'Errore durante la creazione della lezione', [], 500);
    }

    public function update($id)
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $dateTime = trim($data['date'] ?? '');
        $duration = isset($data['duration']) ? (int) $data['duration'] : 0;
        $topic = trim($data['topic'] ?? '');
        $price = isset($data['price']) && $data['price'] !== '' ? (float) $data['price'] : null;
        $isPaid = !empty($data['is_paid']);
        $locationType = $data['location_type'] ?? 'in_person';
        $meetingLink = trim($data['meeting_link'] ?? '');

        if ($dateTime === '' || !$duration || $topic === '') {
            $this->jsonResponse(false, 'Dati lezione mancanti o non validi', [], 400);
        }

        try {
            $lessonDate = new DateTimeImmutable($dateTime);
        } catch (\Exception $e) {
            $this->jsonResponse(false, 'Data lezione non valida', [], 400);
        }

        $now = new DateTimeImmutable('now');
        $status = $lessonDate > $now ? 'scheduled' : 'completed';

        if ($locationType !== 'online' && $locationType !== 'in_person') {
            $locationType = 'in_person';
        }

        if ($locationType === 'online' && $meetingLink === '') {
            $this->jsonResponse(false, 'Per una lezione online è necessario specificare un link di accesso', [], 400);
        }

        if ($locationType === 'in_person') {
            $meetingLink = null;
        }

        $lessonModel = new Lesson();
        $success = $lessonModel->updateLesson((int) $id, $dateTime, $duration, $topic, $status, $price, $isPaid, $locationType, $meetingLink);

        if ($success) {
            $this->jsonResponse(true, "Lesson updated successfully");
        }

        $this->jsonResponse(false, 'Errore durante l\'aggiornamento della lezione', [], 500);
    }

    public function delete($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $lessonModel = new Lesson();
        $success = $lessonModel->delete((int) $id);

        if ($success) {
            $this->jsonResponse(true, "Lesson deleted successfully");
        }

        $this->jsonResponse(false, 'Errore durante l\'eliminazione della lezione', [], 500);
    }
}
