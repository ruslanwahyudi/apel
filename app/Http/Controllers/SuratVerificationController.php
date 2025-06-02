<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuratVerificationController extends Controller
{
    /**
     * Verify a surat document
     * 
     * @param string $token The verification token
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verify($token, Request $request)
    {
        // Get the file path from storage (would be mapped to a database in production)
        $filePath = "surat_verification/{$token}.pdf";
        
        // Check if file exists
        if (Storage::exists($filePath)) {
            // If view parameter is set, show verification page instead of document
            if ($request->has('view') && $request->input('view') == 'true') {
                return view('verification.index', ['token' => $token]);
            }
            
            // Return the document
            return Storage::response($filePath);
        }
        
        // Return not found page
        return view('verification.not_found');
    }
    
    /**
     * Store a document for verification
     * 
     * @param string $token The verification token
     * @param string $fileContent The file content (PDF)
     * @return bool
     */
    public static function storeVerificationFile($token, $fileContent)
    {
        // Create directory if it doesn't exist
        if (!Storage::exists('surat_verification')) {
            Storage::makeDirectory('surat_verification');
        }
        
        // Store the file
        $filePath = "surat_verification/{$token}.pdf";
        return Storage::put($filePath, $fileContent);
    }
    
    /**
     * Generate a verification token for a document
     * 
     * @param string $suratId
     * @return string
     */
    public static function generateToken($suratId = null)
    {
        // Generate a unique token
        $token = Str::random(10);
        
        // In a real application, you would save this token to a database
        // along with the surat_id, user_id, and generated timestamp
        
        return $token;
    }
    
    /**
     * Get the verification URL for QR code
     * 
     * @param string $token
     * @return string
     */
    public static function getVerificationUrl($token)
    {
        return url("/verify-surat/{$token}");
    }
} 