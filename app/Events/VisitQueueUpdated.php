<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VisitQueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $action,
        public ?int $visitId = null,
        public ?int $branchId = null,
        public ?string $status = null,
        public ?int $tokenNumber = null,
        public ?string $patientName = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('cliniccare-queue'),
            new Channel('cliniccare-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'visit-queue-updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'visit_id' => $this->visitId,
            'branch_id' => $this->branchId,
            'status' => $this->status,
            'token_number' => $this->tokenNumber,
            'patient_name' => $this->patientName,
            'at' => now()->toIso8601String(),
        ];
    }
}
