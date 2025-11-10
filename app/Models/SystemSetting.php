<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key','value'];

    public static function get(string $key, $default = null)
    {
        $row = static::query()->where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getArray(string $key): array
    {
        $val = static::get($key, '');
        if (!$val) return [];
        if (is_array($val)) return $val;
        // support comma-separated ids or JSON array
        $trim = trim($val);
        if (str_starts_with($trim, '[')) {
            $decoded = json_decode($trim, true);
            return is_array($decoded) ? $decoded : [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $trim)), fn($v) => $v !== ''));
    }
}


