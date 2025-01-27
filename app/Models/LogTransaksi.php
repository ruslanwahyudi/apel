<?php

namespace App\Models;

use App\Models\adm\RegisterSurat;
use App\Models\layanan\Layanan;
use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Monolog\Registry;

class LogTransaksi extends Model
{
    use HasFactory;

    protected $table = 'log_transaksi';
    protected $fillable = ['user_id', 'type_log', 'id_log', 'action', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function surat()
    {
        return $this->belongsTo(RegisterSurat::class, 'id_log', 'id');
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'id_log', 'id');
    }

    public static function insertLog($type_log, $id_log, $action, $description)
    {
        $user = Auth::user();
        LogTransaksi::create([
            'user_id' => $user->id,
            'type_log' => $type_log,
            'id_log' => $id_log,
            'action' => $action,
            'description' => $description
        ]);
    }
}
