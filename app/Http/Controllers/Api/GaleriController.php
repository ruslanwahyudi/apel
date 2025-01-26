<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index()
    {
        $galeri = Gallery::latest()
                ->get()
                ->map(function($item) {
                    $item->image_url = url('api/storage/' . $item->image);
                    return $item;
                });
        
        return response()->json([
            'success' => true,
            'message' => 'List Galeri',
            'data' => $galeri
        ]);
    }

    public function show($id)
    {
        $galeri = Gallery::findOrFail($id);
        $galeri->image_url = url('api/storage/' . $galeri->image);
        
        return response()->json([
            'success' => true,
            'message' => 'Detail Galeri',
            'data' => $galeri
        ]);
    }
} 