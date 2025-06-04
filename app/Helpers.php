<?php

use App\Models\adm\RegisterSurat;
use App\Models\RolePrivilege;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

if (!function_exists('can')) {
    function can($menu, $action)
    {
        $roleId = Auth::user()->role_id;

        // Periksa hak akses
        $privilege = RolePrivilege::where('role_id', $roleId)
            ->whereHas('menu', function ($query) use ($menu) {
                $query->where('name', $menu);
            })
            ->first();

        return $privilege && $privilege->$action;
    }


    function getNoSurat()
    {
        $setting = Setting::instance();
        return $setting->no_surat;
    }
    
    function generateNoSurat()
    {
        // Ambil setting no_surat
        $setting = Setting::instance();
        $noSuratSetting = $setting->no_surat ?? 'DESA';
        
        // Ambil jumlah total surat dari register_surat
        // $jumlahSurat = RegisterSurat::count();
        $nomorUrut = RegisterSurat::max('urut_register');
        if ($nomorUrut == null) {
            $nomorUrut = 114;
        }
        $nomorUrut = str_pad($nomorUrut + 1, 3, '0', STR_PAD_LEFT);
        
        // Format bulan dan tahun
        $bulan = date('m'); // Format: 01, 02, dst
        $tahun = date('Y');  // Format: 2025
        
        // Format final: 001/DESA/01/2025
        return "{$nomorUrut}/{$noSuratSetting}/{$bulan}/{$tahun}";
    }

    function generateUrutRegister()
    {
        $nomorUrut = RegisterSurat::max('urut_register');
        if ($nomorUrut == null) {
            $nomorUrut = 114;
        }
        $nomorUrut = $nomorUrut+1;
        return $nomorUrut;
    }

    
}


?>