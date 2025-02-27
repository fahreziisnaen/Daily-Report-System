<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'report_date',
        'project_code',
        'location',
        'start_time',
        'end_time',
        'is_overnight'
    ];

    protected $casts = [
        'report_date' => 'date',
        'is_overnight' => 'boolean'
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ReportDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 