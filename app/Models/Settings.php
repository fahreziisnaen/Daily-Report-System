<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'description'
    ];

    // Helper method untuk mengambil value setting
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;
        
        // Handle boolean values
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        return $value;
    }

    // Helper method untuk set/update value
    public static function set($key, $value)
    {
        // Ensure boolean values are stored consistently
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $setting = static::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            static::create([
                'key' => $key,
                'value' => $value,
                'group' => 'general'
            ]);
        }
    }
} 