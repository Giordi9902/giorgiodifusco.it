<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonPackage;
use App\Models\Payment;
use App\Models\StudentNote;

class StudentController extends Controller
{
    public function report()
    {
        $this->requireAuth();
        $studentId = $this->currentUserId();
        $studentNoteModel = new StudentNote();
        $packageModel = new LessonPackage();
        $lessonModel = new Lesson();
        $paymentModel = new Payment();
        $notes = $studentNoteModel->getForStudent($studentId);
        $unpaidPackages = $packageModel->sumUnpaid($studentId);
        $unpaidSingleLessons = $lessonModel->sumUnpaidSingleLessons($studentId);
        $totalPayments = $paymentModel->sumForStudent($studentId);
        $pendingBalance = $unpaidPackages + $unpaidSingleLessons;
        $this->view('student/report', [
            'notes' => $notes,
            'stats' => [
                'unpaid_packages' => $unpaidPackages,
                'unpaid_single_lessons' => $unpaidSingleLessons,
                'pending_balance' => $pendingBalance,
                'total_paid' => $totalPayments,
            ]
        ]);
    }

    public function index()
    {
        $this->requireAdmin();
        $userModel = new User();
        $students = $userModel->getStudents();
        $this->jsonResponse(true, "Students retrieved", $students);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $name = trim((string) ($data['name'] ?? ''));
        $email = filter_var(trim((string) ($data['email'] ?? '')), FILTER_VALIDATE_EMAIL);
        $password = (string) ($data['password'] ?? '');

        if ($name === '' || !$email || $password === '') {
            $this->jsonResponse(false, "Missing required fields", [], 400);
        }
        if (strlen($password) < 8) {
            $this->jsonResponse(false, "Password must be at least 8 characters", [], 400);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $this->jsonResponse(false, "Email already exists", [], 400);
        }

        $success = $userModel->create($name, $email, $password);
        if ($success) {
            $this->jsonResponse(true, "Student created successfully", [], 201);
        } else {
            $this->jsonResponse(false, "Failed to create student", [], 500);
        }
    }

    public function update($id)
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $name = trim((string) ($data['name'] ?? ''));
        $email = filter_var(trim((string) ($data['email'] ?? '')), FILTER_VALIDATE_EMAIL);

        if ($name === '' || !$email) {
            $this->jsonResponse(false, "Missing required fields", [], 400);
        }

        $userModel = new User();
        $existing = $userModel->findByEmail($email);
        if ($existing && (int) $existing['id'] !== (int) $id) {
            $this->jsonResponse(false, "Email already exists", [], 400);
        }

        $success = $userModel->update((int) $id, $name, $email);

        if ($success) {
            $this->jsonResponse(true, "Student updated successfully");
        } else {
            $this->jsonResponse(false, "Failed to update student", [], 500);
        }
    }

    public function delete($id)
    {
        $this->requireAdmin();
        $data = $this->input();
        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $userModel = new User();
        $success = $userModel->delete((int) $id);

        if ($success) {
            $this->jsonResponse(true, "Student deleted successfully");
        } else {
            $this->jsonResponse(false, "Failed to delete student", [], 500);
        }
    }

    public function dashboard()
    {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $this->redirect('/admin');
        }
        $studentId = $this->currentUserId();

        $lessonModel = new Lesson();
        $packageModel = new LessonPackage();
        $paymentModel = new Payment();
        $totalPayments = $paymentModel->sumForStudent($studentId);

        $packageStats = $packageModel->getAggregatedForStudent($studentId);
        $totalMinutes = $packageStats['total_minutes'] ?? 0;
        $usedMinutes = $packageStats['used_minutes'] ?? 0;
        $remainingMinutes = max(0, $totalMinutes - $usedMinutes);

        $completedMinutes = $lessonModel->sumCompletedMinutesForStudent($studentId);

        $unpaidPackages = $packageModel->sumUnpaid($studentId);
        $unpaidSingleLessons = $lessonModel->sumUnpaidSingleLessons($studentId);
        $pendingBalance = $unpaidPackages + $unpaidSingleLessons;
        $stats = [
            'lessons_total' => $lessonModel->countForStudent($studentId),
            'lessons_scheduled' => $lessonModel->countForStudentByStatus($studentId, 'scheduled'),
            'next_lesson' => $lessonModel->getNextLessonForStudent($studentId),
            'total_paid' => $totalPayments,
            'packages_total' => $packageStats['total_packages'] ?? 0,
            'packages_active' => $packageStats['active_packages'] ?? 0,
            'packages_total_minutes' => $totalMinutes,
            'packages_used_minutes' => $usedMinutes,
            'packages_remaining_minutes' => $remainingMinutes,
            'lessons_completed_minutes' => $completedMinutes,
            'pending_balance' => $pendingBalance,
        ];

        $this->view('student/dashboard', ['stats' => $stats]);
    }

    public function lessonsPage()
    {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $this->redirect('/admin/lessons');
        }
        $this->view('student/lessons');
    }

    public function packagesPage()
    {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $this->redirect('/admin');
        }
        $this->view('student/packages');
    }

    public function materialsPage()
    {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $this->redirect('/admin/materials');
        }
        $this->view('student/materials');
    }

    public function paymentsPage()
    {
        $this->requireAuth();
        if ($this->isAdmin()) {
            $this->redirect('/admin/payments');
        }
        $this->view('student/payments');
    }
}
