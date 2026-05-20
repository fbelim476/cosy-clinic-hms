<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'address', 'city', 'state', 'pincode',
        'phone', 'email', 'gst_number', 'logo_path', 'is_active',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
}
