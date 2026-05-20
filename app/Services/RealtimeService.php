<?php

namespace App\Services;

use App\Events\DashboardStatsUpdated;
use App\Events\HmsNotification;
use App\Events\VisitQueueUpdated;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\PharmacyOrder;
use App\Models\User;
use App\Enums\VisitStatus;

class RealtimeService
{
    public static function queueUpdated(
        string $action,
        ?PatientVisit $visit = null,
        ?string $message = null,
    ): void {
        event(new VisitQueueUpdated(
            action: $action,
            visitId: $visit?->id,
            branchId: $visit?->branch_id,
            status: $visit?->status?->value ?? $visit?->status,
            tokenNumber: $visit?->token_number,
            patientName: $visit?->patient?->name,
        ));

        self::refreshDashboardStats();

        if ($message && $visit) {
            self::notifyRole('receptionist', 'Queue Update', $message, 'info');
            if (in_array($action, ['sent_to_doctor', 'consultation_started'])) {
                self::notifyRole('doctor', 'New Patient', $message, 'warning');
            }
            if ($action === 'sent_to_pharmacy') {
                self::notifyRole('pharmacist', 'Pharmacy Queue', $message, 'success');
            }
        }
    }

    public static function refreshDashboardStats(): void
    {
        event(new DashboardStatsUpdated(stats: [
            'patients_total' => Patient::count(),
            'visits_today' => PatientVisit::whereDate('created_at', today())->count(),
            'waiting' => PatientVisit::where('status', VisitStatus::Waiting)->whereDate('created_at', today())->count(),
            'with_doctor' => PatientVisit::where('status', VisitStatus::WithDoctor)->whereDate('created_at', today())->count(),
            'at_pharmacy' => PatientVisit::where('status', VisitStatus::AtPharmacy)->whereDate('created_at', today())->count(),
            'revenue_today' => (float) Invoice::whereDate('created_at', today())->sum('paid_amount'),
            'pharmacy_today' => (float) PharmacyOrder::where('status', 'completed')->whereDate('created_at', today())->sum('total'),
            'emergency' => PatientVisit::where('priority', 'emergency')->whereDate('created_at', today())->where('status', '!=', VisitStatus::Completed->value)->count(),
        ]));
    }

    public static function notifyUser(int $userId, string $title, string $message, string $type = 'info'): void
    {
        event(new HmsNotification($userId, $title, $message, $type));
    }

    public static function notifyRole(string $role, string $title, string $message, string $type = 'info'): void
    {
        User::role($role)->where('is_active', true)->each(
            fn (User $user) => self::notifyUser($user->id, $title, $message, $type)
        );
    }

    public static function paymentReceived(?int $userId, string $invoiceNumber, float $amount): void
    {
        self::refreshDashboardStats();
        if ($userId) {
            self::notifyUser($userId, 'Payment Received', "₹{$amount} received for {$invoiceNumber}", 'success');
        }
        self::notifyRole('accountant', 'Payment', "₹{$amount} — {$invoiceNumber}", 'success');
    }
}
