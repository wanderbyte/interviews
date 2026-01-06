<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EncryptDecryptMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        /* =======================
           DECRYPT REQUEST (GET)
        ======================= */
        if ($request->has('payload')) {
            $decrypted = Crypt::decryptString($request->query('payload'));
            $request->merge(json_decode($decrypted, true));
        }

        $response = $next($request);

        /* =======================
           ENCRYPT RESPONSE
        ======================= */
        $encrypted = Crypt::encryptString(
            json_encode($response->getData())
        );

        return response()->json([
            'payload' => $encrypted
        ]);
    }
}
