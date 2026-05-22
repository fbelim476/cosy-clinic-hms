<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DashboardStatsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $stats = []) {}

    public function broadcastOn(): array
    {
        return [new Channel('CosyClinic-dashboard')];
    }

    public function broadcastAs(): string
    {
        return 'dashboard-stats-updated';
    }

    public function broadcastWith(): array
    {
        return ['stats' => $this->stats, 'at' => now()->toIso8601String()];
    }
}
