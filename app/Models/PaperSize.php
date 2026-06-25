<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaperSize extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'width_mm', 'height_mm', 'margins',
        'orientation', 'scale', 'dpi', 'padding', 'is_system', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'width_mm' => 'decimal:2',
            'height_mm' => 'decimal:2',
            'scale' => 'decimal:2',
            'margins' => 'array',
            'padding' => 'array',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function templates(): HasMany
    {
        return $this->hasMany(PrintTemplate::class);
    }

    public function printerProfiles(): HasMany
    {
        return $this->hasMany(PrinterProfile::class);
    }
}
