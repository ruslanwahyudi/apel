<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KegiatanPembangunan;
use App\Models\ProgresPembangunan;
use App\Models\FotoProgresPembangunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgresPembangunanController extends Controller
{
    public function create($kegiatanId)
    {
        $kegiatan = KegiatanPembangunan::findOrFail($kegiatanId);
        return view('admin.pembangunan.progres.create', compact('kegiatan'));
    }

    public function store(Request $request, $kegiatanId)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'persentase' => 'required|numeric|min:0|max:100',
            'keterangan' => 'required|string',
            'fotos.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'captions.*' => 'nullable|string|max:255'
        ]);

        $kegiatan = KegiatanPembangunan::findOrFail($kegiatanId);

        $progres = ProgresPembangunan::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => auth()->id(),
            'tanggal' => $request->tanggal,
            'persentase' => $request->persentase,
            'keterangan' => $request->keterangan
        ]);

        if ($request->hasFile('fotos')) {
            foreach($request->file('fotos') as $index => $foto) {
                $filename = 'progres-' . Str::slug($kegiatan->nama_kegiatan) . '-' . time() . '-' . $index . '.' . $foto->getClientOriginalExtension();
                
                $foto->storeAs('public/pembangunan', $filename);

                FotoProgresPembangunan::create([
                    'progres_id' => $progres->id,
                    'foto' => $filename,
                    'caption' => $request->captions[$index] ?? null
                ]);
            }
        }

        // Update status kegiatan jika persentase 100%
        if ($request->persentase == 100) {
            $kegiatan->update(['status' => 'Selesai']);
        } else if ($kegiatan->status == 'Belum Dimulai') {
            $kegiatan->update(['status' => 'Dalam Pengerjaan']);
        }

        return redirect()
            ->route('admin.pembangunan')
            ->with('success', 'Progres pembangunan berhasil ditambahkan');
    }
} 