<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PrintSetting extends Model
{
    protected $fillable = ['branch_id', 'key', 'value', 'group'];

    public static function get(string $key, mixed $default = null, ?int $branchId = null): mixed
    {
        $branchId = $branchId ?? 1;

        return Cache::remember("print_setting.{$branchId}.{$key}", 3600, function () use ($key, $default, $branchId) {
            $row = static::where('branch_id', $branchId)->where('key', $key)->first();

            return $row?->value ?? $default;
        });
    }

    public static function set(string $key, mixed $value, string $group = 'general', ?int $branchId = null): void
    {
        $branchId = $branchId ?? 1;
        static::updateOrCreate(
            ['branch_id' => $branchId, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value) : $value, 'group' => $group]
        );
        Cache::forget("print_setting.{$branchId}.{$key}");
    }
}
