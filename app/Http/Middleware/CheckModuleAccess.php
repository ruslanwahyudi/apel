<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, $module, $crud)
    {
        $user = Auth::user();
        $module = Module::where('name', $module)->first();

        if (!$module || !$user->hasAccessToModule($module->id, $crud)) {
            return redirect()->route('unauthorized');  // Redirect jika tidak punya akses
        }

        return $next($request);
    }
}
