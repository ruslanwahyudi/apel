<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function testPelayananData($id)
    {
        // Get data from duk_pelayanan directly
        $pelayananData = DB::table('duk_pelayanan')
            ->where('id', $id)
            ->first();
        
        // Get data identitas from relational table
        $dataIdentitas = DB::table('duk_data_identitas_pemohon as ddip')
            ->join('duk_identitas_pemohon as dip', 'ddip.identitas_pemohon_id', '=', 'dip.id')
            ->where('ddip.pelayanan_id', $id)
            ->select('dip.nama_field', 'ddip.nilai')
            ->get();
        
        // Convert to array format for testing
        $dukData = [];
        foreach ($dataIdentitas as $data) {
            $dukData[$data->nama_field] = $data->nilai ?? '';
        }
        
        // Prepare response with detailed information
        $response = [
            'pelayanan_raw' => $pelayananData,
            'pelayanan_array' => $pelayananData ? (array)$pelayananData : [],
            'duk_data' => $dukData,
            'duk_raw' => $dataIdentitas,
            'merged_data' => array_merge(
                $dukData,
                $pelayananData ? (array)$pelayananData : []
            )
        ];
        
        // Log for debugging
        Log::info('Test Pelayanan Data', $response);
        
        return response()->json($response);
    }
} 