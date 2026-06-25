<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FontLibrary extends Model
{
    protected $table = 'font_library';

    protected $fillable = ['name', 'family', 'source', 'file_path', 'is_system', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
