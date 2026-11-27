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
            $cookieLocale = trim((string) $request->cookie('mq_locale', ''));
            $default_locale = 'en';
            $is_rtl = 0;
            try {
                $language = $cookieLocale !== '' ? Language::where('code', $cookieLocale)->first() : null;
                if (!$language) {
                    $language = Language::where('code', 'en')->first() ?? Language::first();
                }
                if ($language) {
                    $default_locale = $language->code ?? 'en';
                    $is_rtl = (int) ($language->is_rtl ?? (in_array($default_locale, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0));
                }
            } catch (\Throwable $e) {
                $default_locale = $cookieLocale !== '' ? $cookieLocale : 'en';
                $is_rtl = in_array($default_locale, ['ar', 'he', 'fa', 'ur'], true) ? 1 : 0;
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
