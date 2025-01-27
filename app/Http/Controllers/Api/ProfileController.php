<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Storage;

class ProfileController extends Controller
{


    public function show(Request $request)
    {
        $user = User::with('profile')->findOrFail($request->user()->id);
        
        // Cek apakah foto sudah berupa URL lengkap
        if ($user->profile && $user->profile->foto) {
            if (filter_var($user->profile->foto, FILTER_VALIDATE_URL)) {
                // Jika sudah URL lengkap, gunakan langsung
                $user->profile->foto = $user->profile->foto;
            } else {
                // Jika bukan URL lengkap, gunakan getFotoUrlAttribute
                $user->profile->foto = $user->profile->getFotoUrlAttribute();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile data',
            'data' => $user
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'nullable|string|max:16',
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable',
            'jenis_kelamin' => 'nullable|string',
            'agama' => 'nullable|string',
            'status_pernikahan' => 'nullable|string',
            'kewarganegaraan' => 'nullable|string|max:255',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $user->profile->update($request->all());

        $user->profile = $user->profile();

        return response()->json([
            'success' => true,
            'message' => 'Profile berhasil diupdate',
            'data' => $user
        ]);
    }

    public function updatePhotoProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = $request->user();
        $profile = UserProfile::where('user_id', $user->id)->first();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($profile->foto && $profile->foto != 'default.png') {
                Storage::delete('public/profile/' . $profile->foto);
            }

            $file = $request->file('foto');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/profile', $filename);

            $profile->update([
                'foto' => $filename
            ]);

        }

        if($profile->foto){
            $profile->foto = $profile->getFotoUrlAttribute();
        }else{
            $profile->foto = $profile->getFotoUrlAttribute().'/default.png';
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profile berhasil diupload',
            'data' => [
                'user' => $user,
                'profile' => $profile
            ]
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::findOrFail($request->user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }
} 