<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Flatten comma-separated roles if passed as a single string (e.g. 'owner,manager')
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowedRoles[] = strtolower(trim($r));
            }
        }

        if (! empty($allowedRoles) && ! in_array(strtolower($userRoleValue), $allowedRoles, true)) {
            abort(403, 'Akses ditolak. Peran Anda ('.$userRoleValue.') tidak diizinkan mengakses halaman ini.');
        }

        return $next($request);
    }
}
