<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrintTemplate extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';

    protected $fillable = [
        'branch_id', 'name', 'slug', 'document_type', 'paper_size_id', 'printer_profile_id',
        'status', 'version', 'layout', 'header', 'footer', 'theme', 'settings',
        'is_default', 'created_by', 'updated_by', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'layout' => 'array',
            'header' => 'array',
            'footer' => 'array',
            'theme' => 'array',
            'settings' => 'array',
            'is_default' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function paperSize(): BelongsTo
    {
        return $this->belongsTo(PaperSize::class);
    }

    public function printerProfile(): BelongsTo
    {
        return $this->belongsTo(PrinterProfile::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PrintTemplateVersion::class)->orderByDesc('version');
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
