<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QueueDisplay extends Model
{
    public $timestamps = false;

    protected $fillable = ['branch_id', 'department_id', 'current_token', 'waiting_count'];
}
