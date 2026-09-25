<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Lesson;
use App\Models\LessonPackage;
use App\Models\Payment;
use App\Models\BlogInsights;

class AdminController extends Controller
{
    public function report()
    {
        $this->requireAdmin();
        $this->view('admin/report');
    }

    public function index()
    {
        $this->requireAdmin();

        $userModel = new User();
        $lessonModel = new Lesson();
        $packageModel = new LessonPackage();
        $paymentModel = new Payment();

        $packageStats = $packageModel->getAggregatedAll();
        $totalMinutes = $packageStats['total_minutes'];
        $usedMinutes = $packageStats['used_minutes'];

        $stats = [
            'students_count' => $userModel->countStudents(),
            'lessons_total' => $lessonModel->countAll(),
            'lessons_scheduled' => $lessonModel->countByStatus('scheduled'),
            'payments_total_amount' => $paymentModel->sumAll(),
            'packages_total' => $packageStats['total_packages'],
            'packages_active' => $packageStats['active_packages'],
            'packages_total_minutes' => $totalMinutes,
            'packages_used_minutes' => $usedMinutes,
            'packages_remaining_minutes' => max(0, $totalMinutes - $usedMinutes),
            'pending_balance' => $packageModel->sumUnpaid() + $lessonModel->sumUnpaidSingleLessons(),
        ];

        // Insights blog: periodo scelto con ?periodo=7|30|90 (giorni)
        $days = (int) ($_GET['periodo'] ?? 30);
        if (!in_array($days, [7, 30, 90], true)) {
            $days = 30;
        }
        try {
            $blog = (new BlogInsights())->getDashboard($days);
        } catch (\Throwable $e) {
            // un problema con le statistiche non deve bloccare la dashboard
            error_log('Blog insights: ' . $e->getMessage());
            $blog = null;
        }

        $this->view('admin/dashboard', ['stats' => $stats, 'blog' => $blog]);
    }

    public function students()
    {
        $this->requireAdmin();
        $this->view('admin/students');
    }

    public function blog()
    {
        $this->requireAdmin();
        $this->redirect('/cms/blog');
    }

    public function blogCreate()
    {
        $this->requireAdmin();
        $this->redirect('/cms/blog/new');
    }

    public function blogEdit($id)
    {
        $this->requireAdmin();
        $this->redirect('/cms/blog/edit/' . (int) $id);
    }

    public function lessons()
    {
        $this->requireAdmin();
        $this->view('admin/lessons');
    }

    public function materials()
    {
        $this->requireAdmin();
        $this->view('admin/materials');
    }

    public function payments()
    {
        $this->requireAdmin();
        $this->view('admin/payments');
    }

    public function studentDetail($id)
    {
        $this->requireAdmin();
        $id = (int) $id;

        $userModel = new User();
        $student = $userModel->findById($id);

        if (!$student || $student['role'] !== 'STUDENT') {
            $this->redirect('/admin/students');
        }

        $lessonModel = new Lesson();
        $lessonModel->updateExpiredLessonsToCompleted($id);
        $packageModel = new LessonPackage();
        $paymentModel = new Payment();

        $packageStats = $packageModel->getAggregatedForStudent($id);
        $totalMinutes = $packageStats['total_minutes'];
        $usedMinutes = $packageStats['used_minutes'];

        $stats = [
            'lessons_total' => $lessonModel->countForStudent($id),
            'lessons_scheduled' => $lessonModel->countForStudentByStatus($id, 'scheduled'),
            'next_lesson' => $lessonModel->getNextLessonForStudent($id),
            'payments_total_amount' => $paymentModel->sumForStudent($id),
            'packages_total' => $packageStats['total_packages'],
            'packages_active' => $packageStats['active_packages'],
            'packages_total_minutes' => $totalMinutes,
            'packages_used_minutes' => $usedMinutes,
            'packages_remaining_minutes' => max(0, $totalMinutes - $usedMinutes),
            'pending_balance' => $packageModel->sumUnpaid($id) + $lessonModel->sumUnpaidSingleLessons($id),
        ];

        $this->view('admin/student_detail', [
            'student' => $student,
            'stats' => $stats
        ]);
    }
}
