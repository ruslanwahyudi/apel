<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles_type extends Model
{
    use HasFactory;
    protected $table = 'roles_type';
    protected $fillable = ['name'];
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_module');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu');
    }

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class);
    }

    public function hasAccessToModule($module_id, $crud)
    {
        return $this->privileges()->where('module_id', $module_id)
                                ->where('crud', $crud)
                                ->where('has_access', true)
                                ->exists();
    }
}
