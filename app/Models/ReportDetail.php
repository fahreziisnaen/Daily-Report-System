<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportDetail extends Model
{
    protected $fillable = [
        'report_id',
        'description',
        'status'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }
} 