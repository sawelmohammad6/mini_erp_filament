<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'business_name',
        'business_logo',
        'currency',
        'phone',
        'email',
        'address',
    ];

    public static function get(): self
    {
        return Cache::rememberForever('settings', function () {
            return static::first() ?? static::create(['business_name' => 'My Business']);
        });
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings'));
    }
}
