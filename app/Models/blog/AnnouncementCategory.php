<?php

namespace App\Models\blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnnouncementCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    // Auto-generate slug from name
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    // Relationship with announcements
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'category_id');
    }
} 