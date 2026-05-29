<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class EnsureMaterialsAreEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $materialsEnabled = Setting::where('key', 'materials_enabled')->value('value') !== '0';

        if (!$materialsEnabled) {
            abort(404);
        }

        return $next($request);
    }
}
