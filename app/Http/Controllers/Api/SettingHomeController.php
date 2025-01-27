<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\informasi\ProfilDesa;
use Illuminate\Http\Request;

class SettingHomeController extends Controller
{
    public function index()
    {
        try {
            $users = User::with('profile')->where('role', 'Kepala')->get();
            
            foreach ($users as $user) {
                if ($user->profile && $user->profile->foto) {
                    $fotoPath = 'storage/profile/' . $user->profile->foto;
                    
                    // Cek apakah file foto ada
                    if (file_exists(public_path($fotoPath))) {
                        $user->profile->foto = url($fotoPath);
                    } else {
                        // Jika tidak ada, gunakan default avatar
                        $user->profile->foto = url('assets/images/default_kades.png');
                    }
                } else {
                    // Jika tidak ada profile atau foto, gunakan default avatar
                    // if (!$user->profile) {
                    //     $user->profile = new \stdClass();
                    // }
                    $user->profile->foto = url('assets/images/default_kades.png');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFaq()
    {
        try {
            $setting = Setting::select('faq')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => [
                    'faq' => $setting->faq ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    

    public function getContact()
    {
        try {
            $profil = ProfilDesa::select('nama_desa', 'email', 'telepon', 'alamat')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Data kontak berhasil dimuat',
                'data' => $profil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPrivacyPolicy()
    {
        try {
            $setting = Setting::select('privacy_policy')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => [
                    'privacy_policy' => $setting->privacy_policy ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDisclaimer()
    {
        try {
            $setting = Setting::select('disclaimer')->first();
            
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => [
                    'disclaimer' => $setting->disclaimer ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAbout()
    {
        try {
            $profil = ProfilDesa::first();
            $profil->foto_kantor = asset(''.$profil->foto_kantor);
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => $profil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLokasi()
    {
        $lokasi = 'https://maps.app.goo.gl/mckokJYsMV1M63T37';
        try {
            // $lokasi = Lokasi::first();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dimuat',
                'data' => $lokasi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}

