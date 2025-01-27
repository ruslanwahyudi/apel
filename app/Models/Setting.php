<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'app_name',
        'app_logo',
        'app_favicon',
        'email',
        'phone',
        'address',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'footer_text',
        'no_surat',
        'faq',
        'disclaimer',
        'privacy_policy'
    ];

    // Get logo URL
    public function getLogoUrlAttribute()
    {
        return $this->app_logo ? asset('storage/' . $this->app_logo) : null;
    }

    // Get favicon URL
    public function getFaviconUrlAttribute()
    {
        return $this->app_favicon ? asset('storage/' . $this->app_favicon) : null;
    }

    // Get instance
    public static function instance()
    {
        return static::first();
    }
} 