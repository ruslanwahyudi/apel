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
        $setting = Setting::instance();
        $noSurat = $setting->no_surat;
        $tahun = date('Y');
        $bulan = date('m');
        // get last number of register surat by year and month
        $lastSurat = RegisterSurat::whereYear('tanggal_surat', $tahun)
            ->whereMonth('tanggal_surat', $bulan)
            ->orderBy('id', 'desc')
            ->first();
        $lastNoSurat = $lastSurat ? explode('/', $lastSurat->nomor_surat) : [];
        $latestRegister = $lastSurat ? $lastNoSurat[0] : 0;
        // $lastNoSurat = $lastNoSurat ? $lastNoSurat[0] : 0;
        $nextNoSurat = $latestRegister + 1;
        $finalNoSurat = $nextNoSurat . '/' . $noSurat . '/' . $bulan . '/' . $tahun;
        return $finalNoSurat;
    }

    
}


?>