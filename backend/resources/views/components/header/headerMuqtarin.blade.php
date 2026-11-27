@php
    use App\Services\TranslationService;
    use App\Services\CartService;
    use App\Services\StoreService;
    use App\Services\SettingService;
    use App\Services\MediaService;
    use App\Models\Store;
    $store_id = session('store_id');
    $store_details = app(StoreService::class)->getCurrentStoreData($store_id);
    $store_details = json_decode($store_details) ?? '';
    $store_settings = app(StoreService::class)->getStoreSettings();
    $home_theme = $store_settings['web_home_page_theme'] ?? 'web_home_page_theme_muqtarin_editorial';
    $language_code = app(TranslationService::class)->getLanguageCode();
    $shipping_settings = app(SettingService::class)->getSettings('shipping_method', true);
    $shipping_settings = json_decode($shipping_settings, true);
    $is_shiprocket_enabled = isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1;
@endphp
<div>
    <input type="hidden" id="store-primary-color" name="store-primary-color"
        value="{{ $store_details[0]->primary_color ?? '#0F4C3A' }}">
    <input type="hidden" id="store-secondary-color" name="store-secondary-color"
        value="{{ $store_details[0]->secondary_color ?? '#22C55E' }}">
    <input type="hidden" id="store-link-active-color" name="store-link-active-color"
        value="{{ $store_details[0]->active_color ?? '#0F4C3A' }}">
    <input type="hidden" id="store-link-hover-color" name="store-link-hover-color"
        value="{{ $store_details[0]->hover_color ?? '#0A3528' }}">

    <!-- MUQTARIN Top Utility Bar -->
    <div class="mq-top-strip">
        <div class="mq-container mq-top-strip-inner">
            <div class="d-flex align-items-center gap-2">
                <ion-icon wire:ignore name="call-outline"></ion-icon>
                <a dir="ltr" href="tel:{{ $settings->support_number }}">{{ $settings->support_number }}</a>
            </div>
            @if (!empty($settings->app_short_description))
                <div class="mq-top-strip-promo d-none d-md-inline-flex">
                    <i class="bx bxs-sparkles"></i>
                    <span>{{ $language_code === 'ar' ? labels('front_messages.site_short_description', $settings->app_short_description) : $settings->app_short_description }}</span>
                </div>
            @endif
            <div class="mq-top-strip-controls">
                <div wire:ignore class="select-wrap language-picker">
                    @if (count($languages) > 1)
                        <ul class="default-option mb-0">
                            <li>
                                <div class="option english">
                                    <span>{{ session('locale') ?? ($languages[0]->code ?? 'en') }}</span>
                                </div>
                            </li>
                        </ul>
                        <ul class="select-ul">
                            @foreach ($languages as $language)
                                <li class="option english changeLang" data-lang-code="{{ $language->code }}">
                                    <div>
                                        <span>{{ $language->code }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="option english">
                            <span>{{ session('locale') ?? ($languages[0]->code ?? 'en') }}</span>
                        </div>
                    @endif
                </div>

                @if (!$is_shiprocket_enabled)
                    <div class="select-wrap currency-picker">
                        @if (count($currencies) > 1)
                            <ul class="default-option mb-0">
                                <li>
                                    <div class="option USD">
                                        <span>{{ session('currency') ?? ($system_settings['currency_setting']['code'] ?? 'USD') }}</span>
                                        <i class="arrow-icon"></i>
                                    </div>
                                </li>
                            </ul>
                            <ul class="select-ul">
                                @foreach ($currencies as $currency)
                                    <li class="option USD changeCurrency" data-currency-code="{{ $currency->code }}">
                                        <div>
                                            <span>{{ $currency->code }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="option USD">
                                <span>{{ session('currency') ?? ($system_settings['currency_setting']['code'] ?? 'USD') }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="select-wrap currency-picker">
                        <div class="option USD">
                            <span>INR</span>
                        </div>
                    </div>
                @endif

                <button type="button" class="mq-theme-toggle" onclick="window.muqtarinStorefrontToggleTheme && window.muqtarinStorefrontToggleTheme()"
                    aria-label="{{ labels('front_messages.toggle_theme', 'Toggle Theme') }}"
                    title="{{ labels('front_messages.toggle_theme', 'Toggle Theme') }}">
                    <i class="bx bx-moon"></i>
                    <span class="d-none d-sm-inline">{{ labels('front_messages.theme', 'Theme') }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MUQTARIN Sticky Header -->
    <header wire:ignore.self class="header mq-header {{ $home_theme === 'web_home_page_theme_muqtarin_glass' ? 'mq-header-glass' : '' }} d-flex align-items-center header-fixed">
        <div id="app_url" data-app-url="{{ route('home') }}"></div>
        @php
            $halfStoreLogoUrl = !empty($store_details[0]->half_store_logo)
                ? app(MediaService::class)->getMediaImageUrl($store_details[0]->half_store_logo, 'STORE_IMG_PATH')
                : '';
            $noImageUrl = asset(config('constants.NO_IMAGE'));
            $img = (!empty($halfStoreLogoUrl) && $halfStoreLogoUrl !== $noImageUrl)
                ? $halfStoreLogoUrl
                : app(MediaService::class)->getMediaImageUrl($settings->logo);
            $img = app(MediaService::class)->dynamic_image($img, 150);
            $store_display_name = !empty($store_details[0]->id)
                ? app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store_details[0]->id, $language_code)
                : ($settings->site_title ?? 'MUQTARIN Store');
            if (empty($store_display_name)) {
                $store_display_name = $settings->site_title ?? 'MUQTARIN Store';
            }
        @endphp
        <div class="mq-container mq-header-row">
            <div class="logo align-self-center">
                <a wire:navigate class="mq-brand-link logoImg" href="{{ customUrl('home') }}">
                    <img src="{{ $img }}" alt="{{ $settings->site_title }}" title="{{ $settings->site_title }}" />
                    <span class="mq-brand-badge d-none d-sm-inline">{{ $store_display_name }}</span>
                </a>
            </div>

            <div class="d-none d-lg-block align-self-center">
                <nav class="navigation" id="AccessibleNav">
                    <ul id="siteNav" class="mq-nav-list">
                        <li class="{{ request()->is('/') || request()->is('home') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('home') }}">{{ labels('front_messages.home', 'Home') }}</a>
                        </li>
                        <li class="{{ request()->is('products*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('products') }}">{{ labels('front_messages.products', 'Products') }}</a>
                        </li>
                        <li class="{{ request()->is('combo-products*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('combo-products') }}">{{ labels('front_messages.combo_products', 'Combo Products') }}</a>
                        </li>
                        <li class="{{ request()->is('offers*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('offers') }}">{{ labels('front_messages.offers', 'Offers') }}</a>
                        </li>
                        <li class="{{ request()->is('sellers*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('sellers') }}">{{ labels('front_messages.sellers', 'Sellers') }}</a>
                        </li>
                        <li class="{{ request()->is('compare*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('compare') }}">{{ labels('front_messages.compare', 'Compare') }}</a>
                        </li>
                        <li class="{{ request()->is('blogs*') ? 'active' : '' }}">
                            <a wire:navigate href="{{ customUrl('blogs') }}">{{ labels('front_messages.blogs', 'Blogs') }}</a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="mq-header-actions icons-col">
                <div class="search-parent iconset">
                    <a href="#;" class="mq-icon-btn search-icon clr-none" data-bs-toggle="offcanvas"
                        data-bs-target="#search-drawer" title="{{ labels('front_messages.search', 'Search') }}">
                        <i class="hdr-icon icon anm anm-search-l"></i>
                    </a>
                </div>

                <div class="account-parent iconset">
                    <div class="mq-icon-btn account-link" title="{{ labels('front_messages.account', 'Account') }}">
                        <i class="hdr-icon icon anm anm-user-al"></i>
                    </div>
                    <div id="accountBox">
                        <div class="customer-links">
                            <ul class="m-0">
                                @auth
                                    <li><a href="{{ customUrl('my-account') }}" wire:navigate><i
                                                class="hdr-icon icon anm anm-user-al"></i>{{ labels('front_messages.my_account', 'My Account') }}</a>
                                    </li>
                                    <li><a href="{{ customUrl('orders') }}" wire:navigate><i
                                                class="anm anm-bag-l hdr-icon icon"></i>{{ labels('front_messages.my_orders', 'My Orders') }}</a>
                                    </li>
                                    <li><a href="{{ customUrl('my-account/wallet') }}" wire:navigate><i
                                                class="anm anm-pay-security hdr-icon icon"></i>{{ labels('front_messages.wallet', 'Wallet') }}</a>
                                    </li>
                                    <li><a class="logout cursor-pointer"><i
                                                class="anm anm-arrow-al-left hdr-icon icon"></i>{{ labels('front_messages.sign_out', 'Sign out') }}</a>
                                    </li>
                                @else
                                    <li><a href="{{ customUrl('login') }}" wire:navigate><i
                                                class="anm anm-arrow-al-right anm-user-al hdr-icon icon"></i>{{ labels('front_messages.sign_in', 'Sign In') }}</a>
                                    </li>
                                    <li><a href="{{ customUrl('register') }}" wire:navigate><i
                                                class="hdr-icon icon anm anm-user-al"></i>{{ labels('front_messages.register', 'Register') }}</a>
                                    </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </div>

                @php
                    $cart_count = '0';
                @endphp
                @auth
                    @php
                        $user_id = auth()->id() ?? 0;
                        $store_id = session('store_id') ?? '';
                        $favorites = getFavorites(user_id: $user_id, store_id: $store_id);
                        $cart_count = app(CartService::class)->getCartCount($user_id, $store_id);
                    @endphp
                    <div class="wishlist-link iconset" title="{{ labels('front_messages.wishlist', 'Wishlist') }}">
                        <a wire:navigate href="{{ customUrl('my-account.favorites') }}" class="mq-icon-btn">
                            <i class="anm anm-heart anm-heart-l hdr-icon icon"></i>
                            <span class="wishlist-count">{{ $favorites['favorites_count'] }}</span>
                        </a>
                    </div>
                @endauth

                <div class="header-cart iconset" title="{{ labels('front_messages.cart', 'Cart') }}">
                    <a href="#;" class="mq-icon-btn header-cart btn-minicart clr-none" data-bs-toggle="offcanvas"
                        data-bs-target="#minicart-drawer" wire:ignore>
                        <i class="hdr-icon icon anm anm-cart-l"></i>
                        <span class="cart-count">{{ $cart_count }}</span>
                    </a>
                </div>

                <button type="button"
                    class="mq-icon-btn menu-icon js-mobile-nav-toggle mobile-nav--open d-lg-none"
                    title="{{ labels('front_messages.menu', 'Menu') }}">
                    <ion-icon wire:ignore class="fs-5" name="menu"></ion-icon>
                </button>
            </div>
        </div>
    </header>

    @if ($home_theme === 'web_home_page_theme_muqtarin_express')
        <div class="mq-header-express-bar">
            <div class="mq-container d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <a href="#;" class="mq-express-search-pill" data-bs-toggle="offcanvas" data-bs-target="#search-drawer">
                    <i class="anm anm-search-l"></i>
                    <span>{{ labels('front_messages.search_products_here', 'Search products, brands, and categories...') }}</span>
                </a>
                <div class="d-none d-md-flex align-items-center gap-3 small fw-semibold" style="color: var(--mq-pine);">
                    <span><i class="bx bx-bolt-circle me-1"></i>{{ labels('front_messages.fast_delivery', 'Express Delivery') }}</span>
                    <span><i class="bx bx-shield-check me-1"></i>{{ labels('front_messages.verified_stores', 'Verified Stores') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Mobile Menu -->
    <div class="mobile-nav-wrapper" role="navigation">
        <div class="closemobileMenu">{{ labels('front_messages.close_menu', 'Close Menu') }}<ion-icon
                name="close-outline" class="icon"></ion-icon></div>
        <ul id="MobileNav" class="mobile-nav">
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('home') }}">{{ labels('front_messages.home', 'Home') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('products') }}">{{ labels('front_messages.products', 'Products') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('combo-products') }}">{{ labels('front_messages.combo_products', 'Combo Products') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('compare') }}">{{ labels('front_messages.compare', 'Compare') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('offers') }}">{{ labels('front_messages.offers', 'Offers') }}</a>
            </li>
            <li class="lvl1 parent megamenu"><a wire:navigate
                    href="{{ customUrl('sellers') }}">{{ labels('front_messages.sellers', 'Sellers') }}</a>
            </li>
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('contact_us') }}">{{ labels('front_messages.contact_us', 'Contact Us') }}</a>
            </li>
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('faqs') }}">{{ labels('front_messages.faqs', 'FAQs') }}</a>
            </li>
            <li class="lvl1 parent dropdown"><a wire:navigate
                    href="{{ customUrl('blogs') }}">{{ labels('front_messages.blogs', 'Blogs') }}</a>
            </li>
            <li class="mobile-menu-bottom">
                <div class="mobile-links">
                    <ul class="list-inline d-inline-flex flex-column w-100">
                        @auth
                            <li><a href="{{ customUrl('my-account') }}" wire:navigate
                                    class="d-flex align-items-center"><i
                                        class="hdr-icon icon anm anm-user-al"></i>{{ labels('front_messages.my_account', 'My Account') }}</a>
                            </li>
                        @else
                            <li><a href="{{ customUrl('login') }}" wire:navigate class="d-flex align-items-center"><i
                                        class="anm anm-arrow-al-right anm-user-al hdr-icon icon"></i>{{ labels('front_messages.sign_in', 'Sign In') }}</a>
                            </li>
                            <li><a href="{{ customUrl('register') }}" wire:navigate class="d-flex align-items-center"><i
                                        class="hdr-icon icon anm anm-user-al"></i>{{ labels('front_messages.register', 'Register') }}</a>
                            </li>
                        @endauth
                        <li class="title h5">{{ labels('front_messages.need_help', 'Need Help?') }}</li>
                        <li><a href="tel:{{ $settings->support_number }}" class="d-flex align-items-center"><ion-icon
                                    name="call-outline" class="me-1"></ion-icon>
                                <span dir="ltr">{{ $settings->support_number }}</span></a></li>
                        <li><a href="mailto:{{ $settings->support_email }}" class="d-flex align-items-center"><ion-icon
                                    name="mail-outline" class="me-1"></ion-icon> {{ $settings->support_email }}</a>
                        </li>
                    </ul>
                </div>
                <div class="mobile-follow mt-2">
                    <h5 class="title">{{ labels('front_messages.follow_us', 'Follow Us') }}</h5>
                    <ul class="list-inline social-icons mt-3">
                        @if ($settings->twitter_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->twitter_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter"><i
                                        class="anm anm-twitter hdr-icon icon"></i></a></li>
                        @endif
                        @if ($settings->facebook_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->facebook_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"><i
                                        class="anm anm-facebook hdr-icon icon"></i></a></li>
                        @endif
                        @if ($settings->instagram_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->instagram_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram"><i
                                        class="anm anm-instagram hdr-icon icon"></i></a></li>
                        @endif
                        @if ($settings->youtube_link !== null)
                            <li class="list-inline-item"><a href="{{ $settings->youtube_link }}"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Youtube"><i
                                        class="anm anm-youtube hdr-icon icon"></i></a></li>
                        @endif
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    {{-- Search --}}
    <livewire:header.SearchProduct />

    {{-- Sticky Multi-Store Switcher --}}
    @if (isset($stores) && count($stores) >= 1)
        <div class="sticky-stores">
            <div class="stores-main">
                <div wire:ignore class="in-out-store-arrow store-show">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </div>
                <div class="stores-container">
                    @foreach ($stores as $store)
                        @php
                            $store_img = route('front_end.dynamic_image', [
                                'url' => app(MediaService::class)->getMediaImageUrl($store->image, 'STORE_IMG_PATH'),
                                'width' => 50,
                                'quality' => 90,
                            ]);
                            $store_name = app(TranslationService::class)->getDynamicTranslation(
                                Store::class,
                                'name',
                                $store->id,
                                $language_code,
                            );
                        @endphp
                        <div class="store-box select-store {{ session('store_id') == $store->id ? 'store-active' : '' }}"
                            title="{{ $store_name }}" data-store-id="{{ $store->id }}"
                            data-store-name="{{ $store_name }}" data-store-slug="{{ $store->slug }}"
                            data-store-image="{{ $store_img }}">
                            <img src="{{ $store_img }}" alt="{{ $store_name }}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <livewire:pages.model-cart />

    @if (isset($stores) && count($stores) >= 2 && empty(session('store_id')))
        <div class="newsletter-modal modal fade" id="store_select_modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-body p-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="newsletter-wrap d-flex flex-column">
                            <div class="newsltr-text text-center store-popup-box">
                                <div class="wraptext mw-100">
                                    <h6 class="title text-transform-none">
                                        {{ labels('front_messages.please_choose_store.', 'Please choose a store.') }}
                                    </h6>
                                    <p class="text">
                                        {{ labels('front_messages.pick_store_that_suits_your_requirements', 'Pick a Store that Suits Your Requirements.') }}
                                    </p>
                                    <div class="collection-style1 row row-cols-2 row-cols-lg-3 mt-0">
                                        @foreach ($stores as $store)
                                            @php
                                                $store_img = route('front_end.dynamic_image', [
                                                    'url' => app(MediaService::class)->getMediaImageUrl($store->image, 'STORE_IMG_PATH'),
                                                    'width' => 400,
                                                    'quality' => 90,
                                                ]);
                                                $store_name = app(TranslationService::class)->getDynamicTranslation(
                                                    Store::class,
                                                    'name',
                                                    $store->id,
                                                    $language_code,
                                                );
                                            @endphp
                                            <div class="category-item zoomscal-hov">
                                                <a data-store-id="{{ $store->id }}"
                                                    data-store-name="{{ $store_name }}"
                                                    data-store-slug="{{ $store->slug }}"
                                                    data-store-image="{{ $store_img }}"
                                                    class="category-link select-store clr-none cursor-pointer">
                                                    <div class="zoom-scal zoom-scal-nopb brands-image">
                                                        <img class="blur-up lazyload w-100"
                                                            data-src="{{ $store_img }}"
                                                            src="{{ $store_img }}" alt="{{ $store_name }}"
                                                            title="{{ $store_name }}" />
                                                    </div>
                                                    <div class="details mt-3 d-flex justify-content-center align-items-center">
                                                        <h4 class="category-title mb-0">{{ $store_name }}</h4>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        #nprogress .bar {
            background: <?= $store_details[0]->secondary_color ?? '#22C55E' ?> !important;
            z-index: 1111;
        }

        .swiper-pagination-bullet-active-main {
            background: <?= $store_details[0]->secondary_color ?? '#0F4C3A' ?> !important;
        }
    </style>

    @if (isset($stores) && count($stores) >= 2 && empty(session('store_id')))
        <script>
            setTimeout(function() {
                $('#store_select_modal').modal("show");
            }, 2000);
        </script>
    @endif
</div>
