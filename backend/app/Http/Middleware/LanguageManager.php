<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('locale')) {
            $default_locale = 'en';
            $is_rtl = 0;
            try {
                $language = Language::where('code', $default_locale)->first();
                if (!$language) {
                    $language = Language::first();
                    $default_locale = $language->code ?? 'en';
                }
                $is_rtl = $language->is_rtl ?? 0;
            } catch (\Throwable $e) {
                $default_locale = 'en';
                $is_rtl = 0;
            }
            session()->put('locale', $default_locale);
            session()->put('is_rtl', $is_rtl);
        }

        $activeLocale = session()->get('locale', 'en');
        if (!session()->has('is_rtl')) {
            try {
                $isRtl = (int) (Language::where('code', $activeLocale)->value('is_rtl') ?? (in_array($activeLocale, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0));
            } catch (\Throwable $e) {
                $isRtl = in_array($activeLocale, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0;
            }
            session()->put('is_rtl', $isRtl);
        }

        App::setLocale($activeLocale);

        return $next($request);
    }
}
