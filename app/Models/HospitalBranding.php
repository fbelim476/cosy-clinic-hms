<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class HospitalBranding extends Model
{
    protected $table = 'hospital_branding';

    protected $fillable = [
        'branch_id', 'hospital_name', 'logo_path', 'small_logo_path', 'watermark_path',
        'hospital_address', 'hospital_phone', 'hospital_email', 'website',
        'gst_number', 'registration_number', 'license_number', 'emergency_contact',
        'footer_note', 'terms_conditions', 'tagline', 'colors',
        'header_config', 'footer_config', 'qr_settings', 'barcode_settings', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'colors' => 'array',
            'header_config' => 'array',
            'footer_config' => 'array',
            'qr_settings' => 'array',
            'barcode_settings' => 'array',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public static function forBranch(?int $branchId = null): ?self
    {
        $branchId = $branchId ?? 1;

        $id = Cache::remember("hospital_branding_id.{$branchId}", 3600, function () use ($branchId) {
            return static::where('branch_id', $branchId)->value('id')
                ?? static::whereNull('branch_id')->value('id');
        });

        return $id ? static::find($id) : null;
    }

    public static function clearCache(?int $branchId = null): void
    {
        $branchId = $branchId ?? 1;
        Cache::forget("hospital_branding_id.{$branchId}");
        Cache::forget("hospital_branding.{$branchId}");
    }
}
