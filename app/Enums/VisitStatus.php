<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Registered = 'registered';
    case Waiting = 'waiting';
    case WithDoctor = 'with_doctor';
    case Prescribed = 'prescribed';
    case AtPharmacy = 'at_pharmacy';
    case Billing = 'billing';
    case LabPending = 'lab_pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Registered => 'Registered',
            self::Waiting => 'Waiting',
            self::WithDoctor => 'With Doctor',
            self::Prescribed => 'Prescribed',
            self::AtPharmacy => 'At Pharmacy',
            self::Billing => 'Billing',
            self::LabPending => 'Lab Pending',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Registered => 'bg-secondary',
            self::Waiting => 'bg-warning text-dark',
            self::WithDoctor => 'bg-info',
            self::Prescribed => 'bg-primary',
            self::AtPharmacy => 'bg-purple',
            self::Billing => 'bg-orange',
            self::LabPending => 'bg-azure',
            self::Completed => 'bg-success',
            self::Cancelled => 'bg-danger',
        };
    }
}
