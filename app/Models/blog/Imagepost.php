<?php

namespace App\Models\blog;

use Illuminate\Database\Eloquent\Model;

class Imagepost extends Model
{
    protected $table = 'blog_image_post';
    
    protected $fillable = [
        'post_id',
        'image'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset($this->image);
    }

    public function post()
    {
        return $this->belongsTo(Posts::class);
    }
}
