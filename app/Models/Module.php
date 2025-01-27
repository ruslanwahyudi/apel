<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';
    protected $fillable = ['name', 'no_urut', 'icon'];

    public function roles()
    {
        return $this->belongsToMany(Roles_type::class, 'role_module');
    }

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'modul_id');
    }
}
