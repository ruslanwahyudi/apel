<?php

namespace App\Models\blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'blog_kategori';
    protected $fillable = ['nama', 'slug'];

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'category_id');
    }

    public function posts()
    {
        return $this->hasMany(Posts::class, 'kategori_id');
    }

    
}
