<?php

namespace App\Models\blog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labelpost extends Model
{
    use HasFactory;
    protected $table = 'blog_label_post';
    protected $fillable = ['label_id', 'post_id'];
}
