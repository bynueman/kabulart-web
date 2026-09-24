<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Switch application language and safely redirect back.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supported = ['id', 'en'];

        if (!in_array($locale, $supported, true)) {
            $locale = config('app.fallback_locale', 'id');
        }

        // Store in session
        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }

        // Store in persistent cookie (1 year = 525,600 minutes)
        cookie()->queue('kabul_locale', $locale, 525600);

        // Safe redirect to previous URL preventing loops
        $previousUrl = url()->previous();
        $currentUrl  = $request->fullUrl();

        if (empty($previousUrl) || $previousUrl === $currentUrl || str_contains($previousUrl, '/locale/')) {
            return redirect('/');
        }

        return redirect()->to($previousUrl);
    }
}
