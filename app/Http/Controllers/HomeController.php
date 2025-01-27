<?php

namespace App\Http\Controllers;

use App\Models\blog\Posts;
use App\Models\Gallery;
use App\Models\informasi\ProfilDesa;
use App\Models\layanan\JenisLayanan;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $berita = Posts::with('kategori', 'labels', 'images')->get();
        $galleries = Gallery::all();
        $pengaturan = Setting::all();
        $services = JenisLayanan::all();
        
        $profil = ProfilDesa::all();
        // dd($profil);
        return view('welcome', compact('berita', 'galleries', 'pengaturan', 'services', 'profil'));
    }
}

