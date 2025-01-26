<?php

namespace App\Http\Controllers;

use App\Models\blog\Announcement;
use App\Models\blog\Posts;
use App\Models\Layanan\Pelayanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Count statistics
        $stats = [
            'announcements' => Announcement::count(),
            'articles' => Posts::count(),
            'services' => Pelayanan::count(),
            'active_users' => User::all()->count(),
            'active_logins' => User::all()->count() // Using Laravel's session table
        ];

        // Get recent service orders
        $recentServices = Pelayanan::with(['user', 'jenisPelayanan', 'dokumenPengajuan', 'dataIdentitas'])
            ->latest()
            ->take(5)
            ->get();

        // Get currently logged in users
        $activeUsers = User::all();

        return view('admin.dashboard', compact('stats', 'recentServices', 'activeUsers'));
    }
}
