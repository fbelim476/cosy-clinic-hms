<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintTemplateVersion extends Model
{
    protected $fillable = [
        'print_template_id', 'version', 'layout', 'header', 'footer',
        'theme', 'settings', 'note', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'layout' => 'array',
            'header' => 'array',
            'footer' => 'array',
            'theme' => 'array',
            'settings' => 'array',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(PrintTemplate::class, 'print_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
