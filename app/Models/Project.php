<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'code',
        'name',
        'customer',
        'status'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'project_code', 'code');
    }

    public function isActive()
    {
        return $this->status === 'Berjalan';
    }
} 