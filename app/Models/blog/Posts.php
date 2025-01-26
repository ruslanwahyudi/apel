<?php

namespace App\Models\blog;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = ['title', 'slug', 'content', 'image', 'kategori_id', 'label_id', 'user_id', 'published_at'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'blog_label_post', 'post_id', 'label_id');
    }

    public function images()
    {
        return $this->hasMany(Imagepost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
