<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $setting = Setting::instance();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'footer_text' => 'nullable|string',
            'no_surat' => 'nullable|string',
            'faq' => 'nullable|string',
            'disclaimer' => 'nullable|string',
            'privacy_policy' => 'nullable|string'
        ]);

        try {
            $setting = Setting::instance();
            $data = $request->except(['app_logo', 'app_favicon']);
            // $data['all'] = $request->all();

            // Handle logo upload
            if ($request->hasFile('app_logo')) {
                if ($setting->app_logo) {
                    Storage::disk('public')->delete($setting->app_logo);
                }
                $data['app_logo'] = $request->file('app_logo')->store('settings', 'public');
            }

            // Handle favicon upload
            if ($request->hasFile('app_favicon')) {
                if ($setting->app_favicon) {
                    Storage::disk('public')->delete($setting->app_favicon);
                }
                $data['app_favicon'] = $request->file('app_favicon')->store('settings', 'public');
            }

            $setting->update($data);

            return redirect()->route('admin.settings')
                ->with('success', 'Pengaturan berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 