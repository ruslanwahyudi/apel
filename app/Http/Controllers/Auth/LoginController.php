<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    protected function authenticated(Request $request, $user)
    {
        // Cek role pengguna dan arahkan ke dashboard yang sesuai
        $this->loadSidebarMenus();
        if ($user->role !== 'user') {
            
            return redirect()->route('admin.dashboard');  // Dashboard admin
        } else {
            return redirect()->route('home');  // Dashboard user
        }
    }

    public function loadSidebarMenus()
    {
        $roleId = Auth::user()->role_id; // Ambil role pengguna
        $moduls = Module::with(['menus' => function ($query) use ($roleId) {
            $query->whereHas('privileges', function ($privilegeQuery) use ($roleId) {
            $privilegeQuery->where('role_id', $roleId)
                           ->where('is_visible', true);
        });
        }])->get();

        // Simpan di session untuk digunakan di sidebar
        session(['sidebarMenus' => $moduls]);
    }


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
