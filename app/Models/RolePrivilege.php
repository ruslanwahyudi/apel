<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class   RolePrivilege extends Model
{
    use HasFactory;
    protected $fillable = ['role_id', 'module_id', 'menu_id', 'can_create', 'can_read', 'can_update', 'can_delete', 'is_visible', 'can_print'];

    public function role()
    {
        return $this->belongsTo(Roles_type::class, 'role_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

}
