<?php

namespace App\Http\Controllers\informasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\informasi\ProfilDesa;
use Auth;
use Storage;
use Str;

class ProfilDesaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $profil = ProfilDesa::orderBy('id', 'desc')->get();
            return response()->json($profil);
        }
        return view('informasi.profil.index');
    }

    public function create()
    {
        return view('informasi.profil.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_desa' => 'required|string|max:255',
            'kode_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'sejarah' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_kantor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profil = ProfilDesa::create([
            'nama_desa' => $request->nama_desa,
            'kode_desa' => $request->kode_desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'alamat' => $request->alamat,
            'kode_pos' => $request->kode_pos,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'sejarah' => $request->sejarah,
            'visi' => $request->visi,
            'misi' => $request->misi,
            'logo' => $request->logo,
            'foto_kantor' => $request->foto_kantor,
        ]);

        if($request->hasFile('logo')) {
            $image = $request->file('logo');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->storeAs('public/images/profil', $imageName);
            $profil->logo = 'storage/images/profil/' . $imageName;
            $profil->save();
        }

        if($request->hasFile('foto_kantor')) {
            $image = $request->file('foto_kantor');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->storeAs('public/images/profil', $imageName);
            $profil->foto_kantor = 'storage/images/profil/' . $imageName;
            $profil->save();
        }

        // return response()->json(['status' => 'success', 'message' => 'Profil desa berhasil ditambahkan']);
        return redirect()->route('info.profil')->with('success', 'Profil desa berhasil ditambahkan');
    }

    public function show(ProfilDesa $profil)
    {
        return response()->json($profil);
    }

    public function edit(ProfilDesa $profil)
    {
        return view('informasi.profil.edit', compact('profil'));
    }

    public function update(Request $request, ProfilDesa $profil)
    {
        $request->validate([
            'nama_desa' => 'required|string|max:255',
            'kode_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:255',
            'telepon' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'sejarah' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'foto_kantor' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $profil->update([
            'nama_desa' => $request->nama_desa,
            'kode_desa' => $request->kode_desa,
            'kecamatan' => $request->kecamatan,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'alamat' => $request->alamat,
            'kode_pos' => $request->kode_pos,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'sejarah' => $request->sejarah,
            'visi' => $request->visi,
            'misi' => $request->misi,
        ]);

        if($request->hasFile('logo')) {
            // Hapus gambar lama jika ada
            if($profil->logo) {
                Storage::delete(str_replace('storage/', 'public/', $profil->logo));
            }
            
            $image = $request->file('logo');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->storeAs('public/images/profil', $imageName);
            $profil->logo = 'storage/images/profil/' . $imageName;
            $profil->save();
        }

        if($request->hasFile('foto_kantor')) {
            // Hapus gambar lama jika ada
            if($profil->foto_kantor) {
                Storage::delete(str_replace('storage/', 'public/', $profil->foto_kantor));
            }
            
            $image = $request->file('foto_kantor');
            $imageName = time() . '_' . uniqid() . '.' . $image->extension();
            $image->storeAs('public/images/profil', $imageName);
            $profil->foto_kantor = 'storage/images/profil/' . $imageName;
            $profil->save();
        }
        // return response()->json(['status' => 'success', 'message' => 'Profil desa berhasil diperbarui']);
        return redirect()->route('info.profil')->with('success', 'Profil desa berhasil diperbarui');
    }

    public function destroy(ProfilDesa $profil)
    {
        if($profil->logo) {
            Storage::delete(str_replace('storage/', 'public/', $profil->logo));
        }

        if($profil->foto_kantor) {
            Storage::delete(str_replace('storage/', 'public/', $profil->foto_kantor));
        }

        
        $profil->delete();
        return response()->json(['status' => 'success', 'message' => 'Profil desa berhasil dihapus']);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $profil = ProfilDesa::where('nama_desa', 'like', '%' . $search . '%')->get();
        return response()->json($profil);
    }
} 