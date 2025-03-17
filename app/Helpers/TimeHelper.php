<?php

namespace App\Helpers;

class TimeHelper
{
    public static function formatHoursToHoursMinutes($hours)
    {
        if (!is_numeric($hours)) return '0 Jam 0 Menit';
        
        $totalMinutes = round($hours * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        return "{$hours} Jam {$minutes} Menit";
    }
} 