<?php

namespace App\Http\Controllers\adm;

use App\Http\Controllers\Controller;
use App\Models\KopSuratConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KopSuratConfigController extends Controller
{
    public function index()
    {
        $configs = KopSuratConfig::orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();
        $activeConfig = KopSuratConfig::getActiveConfig();
        
        return view('adm.kop-surat-config.index', compact('configs', 'activeConfig'));
    }

    public function create()
    {
        return view('adm.kop-surat-config.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'alamat' => 'required|string',
            'website1' => 'required|url',
            'website2' => 'required|string',
            'kepala_desa' => 'nullable|string|max:255',
            'nip_kepala_desa' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['logo']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/assets/images', $filename);
            $data['logo_path'] = 'assets/images/' . $filename;
        } else {
            $data['logo_path'] = 'assets/images/logo-pamekasan.png';
        }

        $config = KopSuratConfig::create($data);

        // If this config is set as active, deactivate others
        if ($request->boolean('is_active')) {
            $config->setAsActive();
        }

        return redirect()
            ->route('adm.kop-surat-config.index')
            ->with('success', 'Konfigurasi kop surat berhasil ditambahkan');
    }

    public function show(KopSuratConfig $kopSuratConfig)
    {
        return view('adm.kop-surat-config.show', compact('kopSuratConfig'));
    }

    public function edit(KopSuratConfig $kopSuratConfig)
    {
        return view('adm.kop-surat-config.edit', compact('kopSuratConfig'));
    }

    public function update(Request $request, KopSuratConfig $kopSuratConfig)
    {
        $request->validate([
            'kabupaten' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desa' => 'required|string|max:255',
            'alamat' => 'required|string',
            'website1' => 'required|url',
            'website2' => 'required|string',
            'kepala_desa' => 'nullable|string|max:255',
            'nip_kepala_desa' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        $data = $request->except(['logo']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists and not default
            if ($kopSuratConfig->logo_path !== 'assets/images/logo-pamekasan.png') {
                Storage::delete('public/' . $kopSuratConfig->logo_path);
            }

            $file = $request->file('logo');
            $filename = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/assets/images', $filename);
            $data['logo_path'] = 'assets/images/' . $filename;
        }

        $kopSuratConfig->update($data);

        // If this config is set as active, deactivate others
        if ($request->boolean('is_active')) {
            $kopSuratConfig->setAsActive();
        }

        return redirect()
            ->route('adm.kop-surat-config.index')
            ->with('success', 'Konfigurasi kop surat berhasil diperbarui');
    }

    public function destroy(KopSuratConfig $kopSuratConfig)
    {
        // Don't allow deletion of active config
        if ($kopSuratConfig->is_active) {
            return redirect()
                ->route('adm.kop-surat-config.index')
                ->with('error', 'Tidak dapat menghapus konfigurasi yang sedang aktif');
        }

        // Delete logo file if not default
        if ($kopSuratConfig->logo_path !== 'assets/images/logo-pamekasan.png') {
            Storage::delete('public/' . $kopSuratConfig->logo_path);
        }

        $kopSuratConfig->delete();

        return redirect()
            ->route('adm.kop-surat-config.index')
            ->with('success', 'Konfigurasi kop surat berhasil dihapus');
    }

    public function setActive(KopSuratConfig $kopSuratConfig)
    {
        $kopSuratConfig->setAsActive();

        return redirect()
            ->route('adm.kop-surat-config.index')
            ->with('success', 'Konfigurasi berhasil diaktifkan');
    }

    public function preview(KopSuratConfig $kopSuratConfig = null)
    {
        if (!$kopSuratConfig) {
            $kopSuratConfig = KopSuratConfig::getActiveConfig();
        }

        return view('adm.kop-surat-config.preview', compact('kopSuratConfig'));
    }
} 