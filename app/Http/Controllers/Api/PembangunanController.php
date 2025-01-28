<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KegiatanPembangunan;
use Illuminate\Http\Request;

class PembangunanController extends Controller
{
    public function index()
    {
        $pembangunan = KegiatanPembangunan::with(['progres' => function($query) {
            $query->latest();
        }])->latest()->get();

        $data = $pembangunan->map(function($item) {
            return [
                'id' => $item->id,
                'nama_kegiatan' => $item->nama_kegiatan,
                'lokasi' => $item->lokasi,
                'anggaran' => $item->anggaran,
                'sumber_dana' => $item->sumber_dana,
                'tanggal_mulai' => $item->tanggal_mulai->format('Y-m-d'),
                'tanggal_selesai' => $item->tanggal_selesai->format('Y-m-d'),
                'pelaksana' => $item->pelaksana,
                'status' => $item->status,
                'progres_terakhir' => $item->progres->first() ? $item->progres->first()->persentase : 0
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar kegiatan pembangunan',
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $pembangunan = KegiatanPembangunan::with(['progres' => function($query) {
            $query->with('fotos')->latest();
        }])->findOrFail($id);

        $data = [
            'id' => $pembangunan->id,
            'nama_kegiatan' => $pembangunan->nama_kegiatan,
            'deskripsi' => $pembangunan->deskripsi,
            'lokasi' => $pembangunan->lokasi,
            'anggaran' => $pembangunan->anggaran,
            'sumber_dana' => $pembangunan->sumber_dana,
            'tanggal_mulai' => $pembangunan->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $pembangunan->tanggal_selesai->format('Y-m-d'),
            'pelaksana' => $pembangunan->pelaksana,
            'status' => $pembangunan->status,
            'progres' => $pembangunan->progres->map(function($progres) {
                return [
                    'tanggal' => $progres->tanggal->format('Y-m-d'),
                    'persentase' => $progres->persentase,
                    'keterangan' => $progres->keterangan,
                    'fotos' => $progres->fotos->map(function($foto) {
                        return [
                            'url' => $foto->foto_url,
                            'caption' => $foto->caption
                        ];
                    })
                ];
            })
        ];

        return response()->json([
            'success' => true,
            'message' => 'Detail kegiatan pembangunan',
            'data' => $data
        ]);
    }
} 