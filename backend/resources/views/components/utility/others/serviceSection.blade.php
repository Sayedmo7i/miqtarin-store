@php
    $langCode = session('locale', 'en');
    $localizeServiceText = function (?string $text, string $labelKey, string $enDefault) use ($langCode) {
        if ($langCode === 'ar' || empty($text) || strcasecmp(trim($text), $enDefault) === 0) {
            $translated = labels('front_messages.' . $labelKey, '');
            if (!empty($translated) && ($langCode === 'ar' || empty($text))) {
                return $translated;
            }
        }
        return !empty($text) ? $text : labels('front_messages.' . $labelKey, $enDefault);
    };
@endphp
<div class="container">
    <div class="service-info row col-row row-cols-lg-4 row-cols-md-4 row-cols-sm-2 row-cols-2 text-center">
        @if ($web_settings['support_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <i class="icon anm anm-phone-call-l"></i>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $localizeServiceText($web_settings['support_title'] ?? '', 'support_service_title', 'Call us any time') }}</h3>
                    <span class="text-muted">{{ $localizeServiceText($web_settings['support_description'] ?? '', 'support_service_desc', 'Contact us 24/7 hours a day') }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['shipping_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <i class="icon anm anm-truck-l"></i>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $localizeServiceText($web_settings['shipping_title'] ?? '', 'shipping_service_title', 'Pickup At Any Store') }}</h3>
                    <span class="text-muted">{{ $localizeServiceText($web_settings['shipping_description'] ?? '', 'shipping_service_desc', 'Free shipping on orders over $65') }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['safety_security_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <i class="icon anm anm-credit-card-l"></i>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $localizeServiceText($web_settings['safety_security_title'] ?? '', 'security_service_title', 'Secured Payment') }}</h3>
                    <span class="text-muted">{{ $localizeServiceText($web_settings['safety_security_description'] ?? '', 'security_service_desc', 'We accept all major credit cards') }}</span>
                </div>
            </div>
        @endif
        @if ($web_settings['return_mode'] == 1)
            <div class="service-wrap col-item">
                <div class="service-icon mb-3">
                    <i class="icon anm anm-redo-l"></i>
                </div>
                <div class="service-content">
                    <h3 class="title mb-2">{{ $localizeServiceText($web_settings['return_title'] ?? '', 'return_service_title', 'Free Returns') }}</h3>
                    <span class="text-muted">{{ $localizeServiceText($web_settings['return_description'] ?? '', 'return_service_desc', '30-days free return policy') }}</span>
                </div>
            </div>
        @endif
    </div>
</div>
