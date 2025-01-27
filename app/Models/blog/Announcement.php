<?php

namespace App\Models\blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Announcement extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'image'
    ];

    // Auto-generate slug from title
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($announcement) {
            $announcement->slug = Str::slug($announcement->title);
        });
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Relationship with category
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'category_id');
    }
} 