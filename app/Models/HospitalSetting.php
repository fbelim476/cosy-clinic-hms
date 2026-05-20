<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HospitalSetting extends Model
{
    protected $fillable = ['branch_id', 'key', 'value', 'group'];

    public static function get(string $key, mixed $default = null, ?int $branchId = null): mixed
    {
        return Cache::remember("setting.{$branchId}.{$key}", 3600, function () use ($key, $default, $branchId) {
            $setting = static::where('key', $key)
                ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
                ->first();

            return $setting?->value ?? $default;
        });
    }
}
