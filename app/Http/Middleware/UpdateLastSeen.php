<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = 'user-last-seen-' . $user->id;

            if (! Cache::has($cacheKey)) {
                $user->updateLastSeen();
                Cache::put($cacheKey, true, now()->addMinutes(1));
            }
        }

        return $next($request);
    }
}
