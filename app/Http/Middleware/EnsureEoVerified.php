<?php

namespace App\Http\Middleware;

use App\Enums\VerificationStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEoVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Anda harus login terlebih dahulu.');
        }

        $organizerProfile = $user->organizerProfile;

        if (
            ! $organizerProfile ||
            $organizerProfile->verification_status !== VerificationStatus::Verified
        ) {
            if ($request->expectsJson()) {
                abort(403, 'Akun Event Organizer Anda belum terverifikasi oleh admin.');
            }

            return redirect()->route('eo.dashboard')->with(
                'error',
                'Akun Anda belum terverifikasi oleh admin. Silakan lengkapi profil organisasi dan tunggu proses verifikasi.'
            );
        }

        return $next($request);
    }
}
