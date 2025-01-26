<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $fillable = ['name','route','modul_id','no_urut','icon'];
    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class, 'modul_id');
    }
    public function roles()
    {
        return $this->belongsToMany(Roles_type::class, 'role_menu');
    }

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class);
    }
}
