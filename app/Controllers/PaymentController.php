<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use App\Models\Payment;
use App\Models\LessonPackage;
use App\Models\Lesson;

class PaymentController extends Controller
{
    public function index()
    {
        $this->requireAuth();

        $paymentModel = new Payment();

        if ($this->isAdmin()) {
            $payments = $paymentModel->getAllForAdmin();
        } else {
            $studentId = $this->currentUserId();
            $payments = $paymentModel->getForStudent($studentId);
        }

        $this->jsonResponse(true, "Payments retrieved", $payments);
    }

    public function store()
    {
        $this->requireAdmin();
        $data = $this->input();

        $this->verifyCsrfToken($data['csrf_token'] ?? '');

        $studentId = isset($data['student_id']) ? (int) $data['student_id'] : 0;
        $amount = isset($data['amount']) ? (float) $data['amount'] : 0.0;
        $date = trim($data['date'] ?? '');
        $method = isset($data['method']) ? trim($data['method']) : null;
        $notes = isset($data['notes']) ? trim($data['notes']) : null;
        $paymentType = ($data['payment_type'] ?? 'single') === 'package' ? 'package' : 'single';
        $packageId = !empty($data['package_id']) ? (int) $data['package_id'] : null;
        $lessonId = !empty($data['lesson_id']) ? (int) $data['lesson_id'] : null;

        if (!$studentId || $amount <= 0 || $date === '') {
            $this->jsonResponse(false, 'Dati pagamento mancanti o non validi', [], 400);
        }

        $paymentModel = new Payment();
        $packageModel = new LessonPackage();
        $lessonModel = new Lesson();
        $package = null;
        $lesson = null;

        if ($paymentType === 'package') {
            $package = $packageId ? $packageModel->findById($packageId) : null;
            if (!$package || (int) $package['student_id'] !== $studentId || (int) $package['is_paid'] === 1 || $package['status'] === 'cancelled') {
                $this->jsonResponse(false, 'Seleziona un pacchetto non ancora pagato dello studente', [], 400);
            }
            $lessonId = null;
        } else {
            $packageId = null;
            if ($lessonId) {
                $lesson = $lessonModel->findById($lessonId);
                if (!$lesson || (int) $lesson['student_id'] !== $studentId || $lesson['lesson_type'] !== 'single' || (int) $lesson['is_paid'] === 1) {
                    $this->jsonResponse(false, 'Lezione selezionata non valida o già pagata', [], 400);
                }
            }
        }

        $db = Database::getInstance()->getConnection();
        try {
            $db->beginTransaction();

            $paymentModel->createPayment($studentId, $amount, $date, $method, $notes, $packageId);

            // Il pacchetto risulta pagato quando i versamenti collegati coprono il prezzo
            if ($package && $paymentModel->sumForPackage($packageId) >= (float) ($package['price'] ?? 0) - 0.005) {
                $packageModel->markPaid($packageId);
            }

            if ($lesson) {
                $lessonModel->markPaid($lessonId);
            }

            $db->commit();
            $this->jsonResponse(true, "Payment registered successfully", [], 201);
        } catch (\Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }

        $this->jsonResponse(false, 'Errore durante la registrazione del pagamento', [], 500);
    }
}
