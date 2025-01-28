<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\adm\KategoriSurat;
use App\Models\Dusun;
use App\Models\User;
use Illuminate\Http\Request;

class DusunController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dusun = Dusun::with('user')->get();

            return response()->json($dusun);
        }

        return view('admin.dusun.index');
    }

    public function create()
    {
        $users = User::where('role', 'Kepala Dusun')->get();
        return view('admin.dusun.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_dusun' => 'required|string|max:255|unique:dusun,nama_dusun',
            'user_id' => 'required|exists:users,id',
            'jumlah_kk' => 'required|integer',
            'jumlah_pr' => 'required|integer',
            'jumlah_lk' => 'required|integer'
        ]);

        Dusun::create($request->all());

        return redirect()
            ->route('admin.dusun')
            ->with('success', 'Dusun berhasil ditambahkan');
    }

    public function edit(Dusun $dusun)
    {
        $users = User::where('role', 'Kepala Dusun')->get();
        return view('admin.dusun.edit', compact('dusun', 'users'));
    }

    public function update(Request $request, Dusun $dusun)
    {
        $request->validate([
            'nama_dusun' => 'required|string|max:255|unique:dusun,nama_dusun,' . $dusun->id,
            'user_id' => 'required|exists:users,id',
            'jumlah_kk' => 'required|integer',
            'jumlah_pr' => 'required|integer',
            'jumlah_lk' => 'required|integer'
        ]);

        $dusun->update($request->all());

        return redirect()
            ->route('admin.dusun')
            ->with('success', 'Dusun berhasil diperbarui');
    }

    public function search(Request $request)
    {
        $dusun = Dusun::where('nama_dusun', 'like', '%' . $request->search . '%')->get();
        return response()->json($dusun);
    }

    public function destroy(Dusun $dusun)
    {
        // if ($dusun->surat()->exists()) {
        //     return redirect()
        //         ->route('admin.dusun')
        //         ->with('error', 'Dusun tidak dapat dihapus karena masih digunakan');
        // }

        $dusun->delete();

        return response()->json(['message' => 'Dusun berhasil dihapus']);
    }
} 
