@php
    use App\Services\MediaService;
    $storeSettings = $storeSettings ?? [];
    $currentTheme = ($storeSettings['web_home_page_theme'] ?? '') ?: 'web_home_page_theme_muqtarin_editorial';

    $presets = [
        [
            'key' => 'editorial',
            'title' => labels('admin_labels.preset_editorial_pine', 'Editorial Pine'),
            'desc' => labels('admin_labels.preset_editorial_pine_desc', 'Curated 2-column hero, spacious editorial cards & bento categories.'),
            'icon' => 'bx-leaf',
            'accent' => '#0F4C3A',
            'secondary' => '#F6C667',
            'theme' => 'web_home_page_theme_muqtarin_editorial',
            'product_card' => 'products_display_style_for_web_6',
            'product_details' => 'web_product_details_style_3',
            'categories' => 'categories_display_style_for_web_4',
            'brands' => 'brands_display_style_for_web_4',
            'wishlist' => 'wishlist_display_style_for_web_3',
        ],
        [
            'key' => 'bento',
            'title' => labels('admin_labels.preset_bento_market', 'Bento Market'),
            'desc' => labels('admin_labels.preset_bento_market_desc', '12-column asymmetric mosaic hero with high-contrast promo tiles.'),
            'icon' => 'bx-grid-alt',
            'accent' => '#14634C',
            'secondary' => '#F6C667',
            'theme' => 'web_home_page_theme_muqtarin_bento',
            'product_card' => 'products_display_style_for_web_6',
            'product_details' => 'web_product_details_style_3',
            'categories' => 'categories_display_style_for_web_4',
            'brands' => 'brands_display_style_for_web_4',
            'wishlist' => 'wishlist_display_style_for_web_3',
        ],
        [
            'key' => 'glass',
            'title' => labels('admin_labels.preset_glass_atelier', 'Glass Atelier'),
            'desc' => labels('admin_labels.preset_glass_atelier_desc', 'Frosted glass header, boutique photography & floating telemetry.'),
            'icon' => 'bx-diamond',
            'accent' => '#0F4C3A',
            'secondary' => '#22C55E',
            'theme' => 'web_home_page_theme_muqtarin_glass',
            'product_card' => 'products_display_style_for_web_6',
            'product_details' => 'web_product_details_style_3',
            'categories' => 'categories_display_style_for_web_4',
            'brands' => 'brands_display_style_for_web_4',
            'wishlist' => 'wishlist_display_style_for_web_3',
        ],
        [
            'key' => 'express',
            'title' => labels('admin_labels.preset_fresh_express', 'Fresh Express'),
            'desc' => labels('admin_labels.preset_fresh_express_desc', 'Compact speed-focused SKU shelf & quick category strips.'),
            'icon' => 'bx-bolt-circle',
            'accent' => '#16A34A',
            'secondary' => '#F59E0B',
            'theme' => 'web_home_page_theme_muqtarin_express',
            'product_card' => 'products_display_style_for_web_7',
            'product_details' => 'web_product_details_style_3',
            'categories' => 'categories_display_style_for_web_4',
            'brands' => 'brands_display_style_for_web_4',
            'wishlist' => 'wishlist_display_style_for_web_3',
        ],
    ];

    $muqtarinThemes = [
        [
            'value' => 'web_home_page_theme_muqtarin_editorial',
            'title' => labels('admin_labels.muqtarin_editorial_pine', 'MUQTARIN Editorial Pine'),
            'subtitle' => labels('admin_labels.theme_desc_editorial', '2-column flagship hero & rhythmic shelf'),
            'img' => 'system_images/theme_muqtarin_editorial.png',
        ],
        [
            'value' => 'web_home_page_theme_muqtarin_bento',
            'title' => labels('admin_labels.muqtarin_bento_market', 'MUQTARIN Bento Market'),
            'subtitle' => labels('admin_labels.theme_desc_bento', 'Mosaic hero + spotlight & offer tiles'),
            'img' => 'system_images/theme_muqtarin_bento.png',
        ],
        [
            'value' => 'web_home_page_theme_muqtarin_glass',
            'title' => labels('admin_labels.muqtarin_glass_atelier', 'MUQTARIN Glass Atelier'),
            'subtitle' => labels('admin_labels.theme_desc_glass', 'Frosted navigation & atmospheric display'),
            'img' => 'system_images/theme_muqtarin_glass.png',
        ],
        [
            'value' => 'web_home_page_theme_muqtarin_express',
            'title' => labels('admin_labels.muqtarin_fresh_express', 'MUQTARIN Fresh Express'),
            'subtitle' => labels('admin_labels.theme_desc_express', 'Compact high-density quick grocery grid'),
            'img' => 'system_images/theme_muqtarin_express.png',
        ],
    ];

    $classicThemes = [
        ['value' => 'web_home_page_theme_1', 'title' => labels('admin_labels.default_theme', 'Default'), 'subtitle' => labels('admin_labels.classic_fashion_store', 'Fashion Store'), 'img' => 'system_images/theme_1.png'],
        ['value' => 'web_home_page_theme_2', 'title' => labels('admin_labels.theme_2', 'Theme 2'), 'subtitle' => labels('admin_labels.classic_prime_pantry', 'Prime Pantry'), 'img' => 'system_images/theme_2.png'],
        ['value' => 'web_home_page_theme_3', 'title' => labels('admin_labels.theme_3', 'Theme 3'), 'subtitle' => labels('admin_labels.classic_luxeline', 'Luxeline'), 'img' => 'system_images/theme_3.png'],
        ['value' => 'web_home_page_theme_4', 'title' => labels('admin_labels.theme_4', 'Theme 4'), 'subtitle' => labels('admin_labels.classic_pharmacy', 'Pharmacy'), 'img' => 'system_images/theme_4.png'],
        ['value' => 'web_home_page_theme_5', 'title' => labels('admin_labels.theme_5', 'Theme 5'), 'subtitle' => labels('admin_labels.classic_electronics', 'Electronics'), 'img' => 'system_images/theme_5.png'],
        ['value' => 'web_home_page_theme_6', 'title' => labels('admin_labels.theme_6', 'Theme 6'), 'subtitle' => labels('admin_labels.classic_grocery_shop', 'Grocery Shop'), 'img' => 'system_images/theme_6.png'],
    ];
@endphp

<div class="mq-presets-panel mb-4">
    <div class="mq-presets-header">
        <div>
            <span class="mq-presets-eyebrow">
                <i class="bx bxs-magic-wand"></i>
                {{ labels('admin_labels.one_click_web_presets', '1-Click MUQTARIN Presets') }}
            </span>
            <p class="mq-presets-sub mb-0">
                {{ labels('admin_labels.one_click_web_presets_desc', 'Configure your entire web storefront (Home Theme, Product Cards, Product Details, Categories, Brands & Wishlist) in one click, or customize each block below.') }}
            </p>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mq-sync-palette-btn" id="mq-sync-pine-palette-btn">
            <i class="bx bx-palette"></i>
            <span>{{ labels('admin_labels.sync_muqtarin_palette', 'Apply Recommended Pine Palette') }}</span>
        </button>
    </div>

    <div class="mq-preset-grid">
        @foreach ($presets as $preset)
            @php
                $isCurrent = $currentTheme === $preset['theme'];
            @endphp
            <button type="button"
                class="mq-preset-card {{ $isCurrent ? 'is-active' : '' }}"
                data-mq-preset="{{ $preset['key'] }}"
                data-theme="{{ $preset['theme'] }}"
                data-product-card="{{ $preset['product_card'] }}"
                data-product-details="{{ $preset['product_details'] }}"
                data-categories="{{ $preset['categories'] }}"
                data-brands="{{ $preset['brands'] }}"
                data-wishlist="{{ $preset['wishlist'] }}">
                <div class="mq-preset-card-head">
                    <span class="mq-preset-icon"><i class="bx {{ $preset['icon'] }}"></i></span>
                    <div class="mq-preset-swatches" aria-hidden="true">
                        <span style="background: {{ $preset['accent'] }};"></span>
                        <span style="background: {{ $preset['secondary'] }};"></span>
                    </div>
                </div>
                <div class="mq-preset-title">{{ $preset['title'] }}</div>
                <div class="mq-preset-desc">{{ $preset['desc'] }}</div>
                <div class="mq-preset-foot">
                    <span class="mq-preset-apply-label">{{ labels('admin_labels.apply_bundle', 'Apply Bundle') }}</span>
                    <span class="mq-preset-active-pill"><i class="bx bx-check-circle"></i> {{ labels('admin_labels.active', 'Active') }}</span>
                </div>
            </button>
        @endforeach
    </div>
</div>

{{-- Synced hidden select preserved for StoreController submission & legacy selectors --}}
<select class="web_home_page_theme form-control form-select d-none" name="web_home_page_theme">
    @foreach ($muqtarinThemes as $t)
        <option value="{{ $t['value'] }}" {{ $currentTheme === $t['value'] ? 'selected' : '' }}>{{ $t['title'] }}</option>
    @endforeach
    @foreach ($classicThemes as $t)
        <option value="{{ $t['value'] }}" {{ $currentTheme === $t['value'] ? 'selected' : '' }}>{{ $t['title'] }}</option>
    @endforeach
</select>

{{-- Visual Theme Cards: MUQTARIN Collection --}}
<div class="mq-theme-group-label">
    <span><i class="bx bxs-sparkles"></i> {{ labels('admin_labels.muqtarin_web_themes', 'MUQTARIN Storefront Themes') }}</span>
    <span class="mq-theme-count-pill">4</span>
</div>
<div class="mq-theme-cards-grid mb-4">
    @foreach ($muqtarinThemes as $t)
        @php $isSelected = $currentTheme === $t['value']; @endphp
        <button type="button"
            class="mq-theme-card is-muqtarin {{ $isSelected ? 'is-selected' : '' }}"
            data-mq-select-target="web_home_page_theme"
            data-mq-option-value="{{ $t['value'] }}"
            aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
            <div class="mq-theme-thumb">
                <img src="{{ app(MediaService::class)->getImageUrl($t['img']) }}" alt="{{ $t['title'] }}" loading="lazy" />
                <span class="mq-theme-tier is-muqtarin">{{ labels('admin_labels.muqtarin_badge', 'MUQTARIN') }}</span>
                <span class="mq-theme-check"><i class="bx bx-check"></i></span>
            </div>
            <div class="mq-theme-meta">
                <strong>{{ $t['title'] }}</strong>
                <span>{{ $t['subtitle'] }}</span>
            </div>
        </button>
    @endforeach
</div>

{{-- Visual Theme Cards: Classic / Legacy Collection --}}
<div class="mq-theme-group-label">
    <span><i class="bx bx-collection"></i> {{ labels('admin_labels.classic_web_themes', 'Classic Storefront Themes') }}</span>
    <span class="mq-theme-count-pill">6</span>
</div>
<div class="mq-theme-cards-grid mq-theme-cards-grid--classic mb-4">
    @foreach ($classicThemes as $t)
        @php $isSelected = $currentTheme === $t['value']; @endphp
        <button type="button"
            class="mq-theme-card {{ $isSelected ? 'is-selected' : '' }}"
            data-mq-select-target="web_home_page_theme"
            data-mq-option-value="{{ $t['value'] }}"
            aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
            <div class="mq-theme-thumb">
                <img src="{{ app(MediaService::class)->getImageUrl($t['img']) }}" alt="{{ $t['title'] }}" loading="lazy" />
                <span class="mq-theme-tier is-classic">{{ labels('admin_labels.classic_badge', 'Classic') }}</span>
                <span class="mq-theme-check"><i class="bx bx-check"></i></span>
            </div>
            <div class="mq-theme-meta">
                <strong>{{ $t['title'] }}</strong>
                <span>{{ $t['subtitle'] }}</span>
            </div>
        </button>
    @endforeach
</div>

{{-- Live Interactive Storefront Preview --}}
@include('admin.pages.forms.partials.muqtarin_preview_frame', [
    'iframeId' => 'web_home_page_theme_iframe',
    'iframeSrc' => url('admin/web_home_page_theme'),
    'badge' => labels('admin_labels.live_home_theme_preview', 'Live Home Page Preview'),
    'height' => '470px',
])
