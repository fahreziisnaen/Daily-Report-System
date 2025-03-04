<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'report_date',
        'project_code',
        'location',
        'start_time',
        'end_time',
        'is_overnight',
        'is_overtime',
        'work_day_type',
        'updated_by'
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_overnight' => 'boolean',
        'is_overtime' => 'boolean',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ReportDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
} 