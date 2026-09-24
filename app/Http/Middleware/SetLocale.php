<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    protected array $supportedLocales = ['id', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $default = config('app.fallback_locale', 'id');
        $locale  = null;

        // 1. Session has highest priority (explicit manual choice)
        if ($request->hasSession() && $request->session()->has('locale')) {
            $candidate = $request->session()->get('locale');
            if (in_array($candidate, $this->supportedLocales, true)) {
                $locale = $candidate;
            }
        }

        // 2. Cookie persistence (persists manual choice across browser sessions)
        if (!$locale && $request->hasCookie('kabul_locale')) {
            $candidate = $request->cookie('kabul_locale');
            if (in_array($candidate, $this->supportedLocales, true)) {
                $locale = $candidate;
                if ($request->hasSession()) {
                    $request->session()->put('locale', $locale);
                }
            }
        }

        // 3. First-time visit: inspect Accept-Language browser header if no manual choice was made
        if (!$locale) {
            $accept = $request->header('Accept-Language');
            if (!empty($accept)) {
                // If browser preferences specifically indicate English
                $prefersEn = false;
                $acceptLower = strtolower($accept);
                $idPos = strpos($acceptLower, 'id');
                $enPos = strpos($acceptLower, 'en');

                if ($enPos !== false && ($idPos === false || $enPos < $idPos)) {
                    $prefersEn = true;
                }

                $locale = $prefersEn ? 'en' : 'id';
            } else {
                $locale = $default;
            }
        }

        if (!in_array($locale, $this->supportedLocales, true)) {
            $locale = $default;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
