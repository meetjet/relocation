<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogExchange
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!File::exists(storage_path('logs/exchange'))) {
                File::makeDirectory(storage_path('logs/exchange'), 0755, true);
            }
            File::put(
                storage_path('logs/exchange/' . date('Y-m-d_H-i-s') . '.json'),
                $request->getContent()
            );
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

        }

        return $next($request);
    }
}
