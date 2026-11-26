<!-- Mobile Sidebar Backdrop -->
<div id="muqtarin-sidebar-backdrop" class="muqtarin-sidebar-backdrop"></div>

<!-- Sidebar -->
<nav class="navbar-vertical navbar" {{ session()->get('is_rtl') == 1 ? 'dir=rtl' : '' }}>
    <div class="nav-scroller">
        @php
            $user = auth()->user();

            use Chatify\ChatifyMessenger;
            use App\Services\MediaService;
            use App\Services\SettingService;
            $setting = app(SettingService::class)->getSettings('system_settings', true);
            $setting = json_decode($setting, true);

            $sms_gateway_settings = app(SettingService::class)->getSettings('sms_gateway_settings');
            $messenger = new ChatifyMessenger();
            $unread = $messenger->totalUnseenMessages();
            $settings = app(SettingService::class)->getSettings('admin_preference', true);

            if (is_string($settings)) {
                $settings = json_decode($settings);
            }

            // Active Section Flags
            $isDashboardActive = Request::is('admin/home') || Request::is('admin/home/*');
            $isStoresActive = Request::is('admin/stores') || Request::is('admin/stores/*') || Request::is('admin/store/custom_fields*');
            $isOrdersActive = Request::is('admin/orders') || Request::is('admin/orders*') || Request::is('admin/order_items*');
            $isCategoriesActive = Request::is('admin/categories') || Request::is('admin/categories/*') || Request::is('categories/bulk_upload');
            $isManageActive = $isOrdersActive || $isCategoriesActive || Request::is('admin/manage_stock*') || Request::is('admin/manage_combo_stock*');
            $isBrandActive = Request::is('admin/brands') || Request::is('admin/brands/*');
            $isSellersActive = Request::is('admin/seller/*') || Request::is('admin/seller/create') || Request::is('admin/sellers') || Request::is('admin/sellers/*');
            $isProductsGroupActive = Request::is('admin/products') || Request::is('admin/products/*') || Request::is('admin/product_faqs') || Request::is('admin/product/product_bulk_upload');
            $isProductsActive = Request::is('admin/taxes*') || Request::is('admin/attributes*') || $isProductsGroupActive;
            $isComboGroupActive = Request::is('admin/combo_products') || Request::is('admin/combo_products*') || Request::is('admin/combo_product_faqs');
            $isComboActive = Request::is('admin/combo_product_attributes*') || $isComboGroupActive;
            $isAffiliateActive = Request::is('admin/manage_affiliate_users') || Request::is('admin/add_affiliate_user') || Request::is('admin/affiliate_users*') || Request::is('admin/affiliate_settings') || Request::is('admin/affiliate/reports') || Request::is('admin/affiliate_policies');
            $isBlogsActive = Request::is('admin/blogs') || Request::is('admin/manage_blogs*');
            $isMediaActive = Request::is('admin/media*') || Request::is('admin/storage_type*');
            $isSliderActive = Request::is('admin/sliders*');
            $isOffersActive = Request::is('admin/offers') || Request::is('admin/offer_sliders*') || Request::is('admin/promo_codes*');
            $isTicketsActive = Request::is('admin/tickets*');
            $isChatActive = Request::is('admin/chat*');
            $isFeaturedActive = Request::is('admin/feature_section*');
            $isCustomersActive = Request::is('admin/customers*');
            $isReturnActive = Request::is('admin/return_request*');
            $isDeliveryActive = Request::is('admin/delivery_boys*');
            $isPaymentReqActive = Request::is('admin/payment_request*');
            $isFaqActive = Request::is('admin/faq*');
            $isNotifActive = Request::is('admin/send_notification*') || Request::is('admin/send_seller_notification*') || Request::is('admin/seller_email_notification*');
            $isCustomMsgActive = Request::is('admin/custom_message*');
            $isLocationActive = Request::is('admin/area/*') || Request::is('admin/pickup_location*') || Request::is('admin/zones*');
            $isSysSettingsActive = Request::is('admin/settings');
            $isWebSettingsActive = Request::is('admin/web_settings*');
            $isSeoActive = Request::is('admin/seo*');
            $isSysUsersActive = Request::is('admin/system_users*') || Request::is('admin/manage_system_users*');
            $isLangActive = Request::is('admin/settings/language*') || Request::is('admin/settings/manage_language*') || Request::is('admin/language/bulk_translation_upload*');
            $isReportsActive = Request::is('admin/reports*') || Request::is('admin/settings/sales_reports/*');

            $chevronSvg = '<svg class="sidebar-section-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';
        @endphp
        <input type="hidden" id="sms_gateway_data" value='{{ $sms_gateway_settings }}' />
        <div class="sidenav-header d-flex align-items-center justify-content-between">
            <a class="navbar-brand m-0" href="{{ route('admin.home') }}">
                <img src="{{ asset('assets/brand/logo.svg') }}" class="navbar-brand-img brand-logo-light" alt="MUQTARIN Store">
                <img src="{{ asset('assets/brand/logo-dark.svg') }}" class="navbar-brand-img brand-logo-dark" alt="MUQTARIN Store">
                <img src="{{ asset('assets/brand/icon.svg') }}" class="navbar-brand-icon" alt="MUQTARIN">
            </a>
            <button type="button" class="btn-close-mobile d-xl-none" id="iconSidenav" aria-label="Close">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <hr class="horizontal dark mt-0">

        <!-- Sidebar Menu Search -->
        <div class="px-2 sidebar-search-wrap">
            <input type="text" class="form-control menuSearch"
                placeholder="{{ labels('admin_labels.search', 'Search') }}">
        </div>

        <ul class="navbar-nav" id="menuList">
            {{-- Dashboard (Direct Primary Link) --}}
            <li class="sidebar-direct nav-item">
                <a class="nav-link {{ $isDashboardActive ? 'active' : '' }}"
                    href="{{ route('admin.home') }}"
                    data-section-title="{{ labels('admin_labels.dashboard', 'Dashboard') }}"
                    title="{{ labels('admin_labels.dashboard', 'Dashboard') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.dashboard', 'Dashboard') }}</span>
                </a>
            </li>

            {{-- 1. Stores --}}
            <li class="sidebar-section {{ $isStoresActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isStoresActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_stores" role="button"
                    aria-expanded="{{ $isStoresActive ? 'true' : 'false' }}" aria-controls="sec_stores"
                    data-section-title="{{ labels('admin_labels.stores', 'Stores') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.stores', 'Stores') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isStoresActive ? 'show' : '' }}" id="sec_stores" data-default-open="{{ $isStoresActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        @if (($settings->store_mode ?? '') != 'single')
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/stores') ? 'active' : '' }}"
                                    href="{{ route('admin.stores.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.add_store', 'Add Store') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view store'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/stores/manage_store') || Request::is('admin/stores/manage_store*') ? 'active' : '' }}"
                                    href="{{ route('admin.stores.manage_store') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.manage_stores', 'Manage Stores') }}</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/store/custom_fields') ? 'active' : '' }}"
                                href="{{ route('admin.custom_fields.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.custom_fields', 'Custom Fields') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 2. Manage (Orders, Categories, Stock) --}}
            <li class="sidebar-section {{ $isManageActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isManageActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_manage" role="button"
                    aria-expanded="{{ $isManageActive ? 'true' : 'false' }}" aria-controls="sec_manage"
                    data-section-title="{{ labels('admin_labels.manage', 'Manage') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.manage', 'Manage') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isManageActive ? 'show' : '' }}" id="sec_manage" data-default-open="{{ $isManageActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view orders'))
                            <li class="nav-item sidebar-subgroup-wrap">
                                <a data-bs-toggle="collapse" href="#order_dropdown"
                                    class="nav-link {{ $isOrdersActive ? 'active' : 'collapsed' }}"
                                    aria-controls="order_dropdown" role="button"
                                    aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.orders_manage', 'Orders Manage') }}</span>
                                    {!! $chevronSvg !!}
                                </a>
                                <div class="collapse {{ $isOrdersActive ? 'show' : '' }}" id="order_dropdown" data-default-open="{{ $isOrdersActive ? 'true' : 'false' }}">
                                    <ul class="nav" data-group-label="{{ labels('admin_labels.orders_manage', 'Orders Manage') }}">
                                        <li class="nav-item {{ Request::is('admin/orders') ? 'active' : '' }}">
                                            <a class="nav-link {{ Request::is('admin/orders') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                                <span class="nav-link-text">{{ labels('admin_labels.orders', 'Orders') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{ Request::is('admin/order_items') || Request::is('admin/order_items*') ? 'active' : '' }}">
                                            <a class="nav-link {{ Request::is('admin/order_items') || Request::is('admin/order_items*') ? 'active' : '' }}" href="{{ route('admin.order_items.index') }}">
                                                <span class="nav-link-text">{{ labels('admin_labels.order_items', 'Order Items') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{ Request::is('admin/orders/order_tracking') ? 'active' : '' }}">
                                            <a class="nav-link {{ Request::is('admin/orders/order_tracking') ? 'active' : '' }}" href="{{ route('admin.orders.order_tracking') }}">
                                                <span class="nav-link-text">{{ labels('admin_labels.order_tracking', 'Order Tracking') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif

                        <li class="nav-item sidebar-subgroup-wrap">
                            <a data-bs-toggle="collapse" href="#category_dropdown"
                                class="nav-link {{ $isCategoriesActive ? 'active' : 'collapsed' }}"
                                aria-controls="category_dropdown" role="button"
                                aria-expanded="{{ $isCategoriesActive ? 'true' : 'false' }}">
                                <span class="nav-link-text">{{ labels('admin_labels.categories', 'Categories') }}</span>
                                {!! $chevronSvg !!}
                            </a>
                            <div class="collapse {{ $isCategoriesActive ? 'show' : '' }}" id="category_dropdown" data-default-open="{{ $isCategoriesActive ? 'true' : 'false' }}">
                                <ul class="nav" data-group-label="{{ labels('admin_labels.categories', 'Categories') }}">
                                    <li class="nav-item {{ Request::is('admin/categories') ? 'active' : '' }}">
                                        <a class="nav-link {{ Request::is('admin/categories') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                            <span class="nav-link-text">{{ labels('admin_labels.categories', 'Categories') }}</span>
                                        </a>
                                    </li>
                                    @if ($user_role == 'super_admin' || $user->hasPermissionTo('view category_order'))
                                        <li class="nav-item {{ Request::is('admin/categories/category_order') || Request::is('admin/categories/category_order/*') ? 'active' : '' }}">
                                            <a class="nav-link {{ Request::is('admin/categories/category_order') || Request::is('admin/categories/category_order/*') ? 'active' : '' }}" href="{{ route('category_order.index') }}">
                                                <span class="nav-link-text">{{ labels('admin_labels.categories_order', 'Categories Order') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item {{ Request::is('admin/categories/category_slider') || Request::is('admin/categories/category_slider/*') ? 'active' : '' }}">
                                        <a class="nav-link {{ Request::is('admin/categories/category_slider') || Request::is('admin/categories/category_slider/*') ? 'active' : '' }}" href="{{ route('category_slider.index') }}">
                                            <span class="nav-link-text">{{ labels('admin_labels.categories_sliders', 'Categories Sliders') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/categories/bulk_upload') || Request::is('admin/categories/bulk_upload/*') || Request::is('categories/bulk_upload') ? 'active' : '' }}"
                                            href="{{ route('categories.bulk_upload') }}">
                                            <span class="nav-link-text">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view stock'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/manage_stock') || Request::is('admin/manage_stock/*') ? 'active' : '' }}"
                                    href="{{ route('admin.manage_stock.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.stock_manage', 'Stock Manage') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view combo_stock'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/manage_combo_stock') || Request::is('admin/manage_combo_stock/*') ? 'active' : '' }}"
                                    href="{{ route('admin.manage_combo_stock.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.combo_stock_manage', 'Combo Stock Manage') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 3. Brand --}}
            <li class="sidebar-section {{ $isBrandActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isBrandActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_brand" role="button"
                    aria-expanded="{{ $isBrandActive ? 'true' : 'false' }}" aria-controls="sec_brand"
                    data-section-title="{{ labels('admin_labels.brand', 'Brand') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.brand', 'Brand') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isBrandActive ? 'show' : '' }}" id="sec_brand" data-default-open="{{ $isBrandActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/brands') ? 'active' : '' }}"
                                href="{{ route('brands.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.brand', 'Brand') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/brands/bulk_upload') || Request::is('admin/brands/bulk_upload/*') ? 'active' : '' }}"
                                href="{{ route('brands.bulk_upload') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 4. Sellers --}}
            <li class="sidebar-section {{ $isSellersActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isSellersActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_sellers" role="button"
                    aria-expanded="{{ $isSellersActive ? 'true' : 'false' }}" aria-controls="sec_sellers"
                    data-section-title="{{ labels('admin_labels.sellers', 'Sellers') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.sellers', 'Sellers') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isSellersActive ? 'show' : '' }}" id="sec_sellers" data-default-open="{{ $isSellersActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/seller/*') || Request::is('admin/seller/create') ? 'active' : '' }}"
                                href="{{ route('admin.sellers.create') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.add_sellers', 'Add Sellers') }}</span>
                            </a>
                        </li>
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view seller'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/sellers') ? 'active' : '' }}"
                                    href="{{ route('sellers.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.sellers', 'Sellers') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view seller_wallet_transaction'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/sellers/seller_wallet_transaction') || Request::is('admin/sellers/seller_wallet_transaction/*') ? 'active' : '' }}"
                                    href="{{ route('admin.sellers.sellerWallet') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.wallet_transactions', 'Wallet Transaction') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 5. Products --}}
            <li class="sidebar-section {{ $isProductsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isProductsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_products" role="button"
                    aria-expanded="{{ $isProductsActive ? 'true' : 'false' }}" aria-controls="sec_products"
                    data-section-title="{{ labels('admin_labels.products', 'Products') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.products', 'Products') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isProductsActive ? 'show' : '' }}" id="sec_products" data-default-open="{{ $isProductsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view tax'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/taxes') || Request::is('admin/taxes/*') ? 'active' : '' }}"
                                    href="{{ route('taxes.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.tax', 'Tax') }}</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/attributes') || Request::is('admin/attributes*') ? 'active' : '' }}"
                                href="{{ route('admin.attributes.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}</span>
                            </a>
                        </li>
                        <li class="nav-item sidebar-subgroup-wrap">
                            <a data-bs-toggle="collapse" href="#products_dropdown"
                                class="nav-link {{ $isProductsGroupActive ? 'active' : 'collapsed' }}"
                                aria-controls="products_dropdown" role="button"
                                aria-expanded="{{ $isProductsGroupActive ? 'true' : 'false' }}">
                                <span class="nav-link-text">{{ labels('admin_labels.products_manage', 'Products Manage') }}</span>
                                {!! $chevronSvg !!}
                            </a>
                            <div class="collapse {{ $isProductsGroupActive ? 'show' : '' }}" id="products_dropdown" data-default-open="{{ $isProductsGroupActive ? 'true' : 'false' }}">
                                <ul class="nav" data-group-label="{{ labels('admin_labels.products_manage', 'Products Manage') }}">
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/products') ? 'active' : '' }}"
                                            href="{{ route('admin.products.index') }}">
                                            <span class="sidenav-normal">{{ labels('admin_labels.add_products', 'Add Products') }}</span>
                                        </a>
                                    </li>
                                    @if ($user_role == 'super_admin' || $user->hasPermissionTo('view product'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('admin/products/manage_product') ? 'active' : '' }}"
                                                href="{{ route('admin.products.manage_product') }}">
                                                <span class="sidenav-normal">{{ labels('admin_labels.manage_products', 'Manage Products') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/product_faqs') ? 'active' : '' }}"
                                            href="{{ route('admin.product_faqs.index') }}">
                                            <span class="sidenav-normal">{{ labels('admin_labels.product_faqs', 'Product FAQs') }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/product/product_bulk_upload') ? 'active' : '' }}"
                                            href="{{ route('admin.product_bulk_upload') }}">
                                            <span class="sidenav-normal">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 6. Combo Products Manage --}}
            <li class="sidebar-section {{ $isComboActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isComboActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_combo_products" role="button"
                    aria-expanded="{{ $isComboActive ? 'true' : 'false' }}" aria-controls="sec_combo_products"
                    data-section-title="{{ labels('admin_labels.combo_products_manage', 'Combo Products Manage') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="m7.5 4.27 9 5.15"/><polyline points="3.29 7 12 12 20.71 7"/><line x1="12" x2="12" y1="22" y2="12"/><circle cx="18.5" cy="15.5" r="2.5"/><path d="M20.27 17.27 22 19"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.combo_products_manage', 'Combo Products Manage') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isComboActive ? 'show' : '' }}" id="sec_combo_products" data-default-open="{{ $isComboActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/combo_product_attributes') || Request::is('admin/combo_product_attributes/*') ? 'active' : '' }}"
                                href="{{ route('admin.combo_product_attributes.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}</span>
                            </a>
                        </li>
                        <li class="nav-item sidebar-subgroup-wrap">
                            <a data-bs-toggle="collapse" href="#combo_products_dropdown"
                                class="nav-link {{ $isComboGroupActive ? 'active' : 'collapsed' }}"
                                aria-controls="combo_products_dropdown" role="button"
                                aria-expanded="{{ $isComboGroupActive ? 'true' : 'false' }}">
                                <span class="nav-link-text">{{ labels('admin_labels.products_manage', 'Products Manage') }}</span>
                                {!! $chevronSvg !!}
                            </a>
                            <div class="collapse {{ $isComboGroupActive ? 'show' : '' }}" id="combo_products_dropdown" data-default-open="{{ $isComboGroupActive ? 'true' : 'false' }}">
                                <ul class="nav" data-group-label="{{ labels('admin_labels.products_manage', 'Products Manage') }}">
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/combo_products') ? 'active' : '' }}"
                                            href="{{ route('admin.combo_products.index') }}">
                                            <span class="sidenav-normal">{{ labels('admin_labels.add_combo_products', 'Add Products') }}</span>
                                        </a>
                                    </li>
                                    @if ($user_role == 'super_admin' || $user->hasPermissionTo('view combo_product'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ Request::is('admin/combo_products/manage_product') ? 'active' : '' }}"
                                                href="{{ route('admin.combo_products.manage_product') }}">
                                                <span class="sidenav-normal">{{ labels('admin_labels.manage_products', 'Manage Products') }}</span>
                                            </a>
                                        </li>
                                    @endif
                                    <li class="nav-item">
                                        <a class="nav-link {{ Request::is('admin/combo_product_faqs') ? 'active' : '' }}"
                                            href="{{ route('admin.combo_product_faqs.index') }}">
                                            <span class="sidenav-normal">{{ labels('admin_labels.product_faqs', 'Product FAQs') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 7. Affiliate System --}}
            <li class="sidebar-section {{ $isAffiliateActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isAffiliateActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_affiliate" role="button"
                    aria-expanded="{{ $isAffiliateActive ? 'true' : 'false' }}" aria-controls="sec_affiliate"
                    data-section-title="{{ labels('admin_labels.affiliate', 'Affiliate System') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.affiliate', 'Affiliate System') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isAffiliateActive ? 'show' : '' }}" id="sec_affiliate" data-default-open="{{ $isAffiliateActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/manage_affiliate_users') || Request::is('admin/add_affiliate_user') || Request::is('admin/affiliate_users*') ? 'active' : '' }}"
                                href="{{ route('admin.affiliate.manage_user') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.affiliate_users', 'Affiliate Users') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/affiliate_settings') ? 'active' : '' }}"
                                href="{{ route('admin.affiliate.settings') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.settings', 'Settings') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/affiliate/reports') ? 'active' : '' }}"
                                href="{{ route('admin.affiliate.reports') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.reports', 'Reports') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/affiliate_policies') ? 'active' : '' }}"
                                href="{{ route('admin.affiliate.policies') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.policies', 'Policies') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 8. Blogs --}}
            <li class="sidebar-section {{ $isBlogsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isBlogsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_blogs" role="button"
                    aria-expanded="{{ $isBlogsActive ? 'true' : 'false' }}" aria-controls="sec_blogs"
                    data-section-title="{{ labels('admin_labels.blogs', 'Blogs') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.blogs', 'Blogs') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isBlogsActive ? 'show' : '' }}" id="sec_blogs" data-default-open="{{ $isBlogsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/blogs') ? 'active' : '' }}"
                                href="{{ route('admin.blogs.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.blog_categories', 'Blog Categories') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/manage_blogs') || Request::is('admin/manage_blogs/*') ? 'active' : '' }}"
                                href="{{ route('manage_blogs.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.create_blog', 'Create Blog') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 9. Media Manage --}}
            <li class="sidebar-section {{ $isMediaActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isMediaActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_media" role="button"
                    aria-expanded="{{ $isMediaActive ? 'true' : 'false' }}" aria-controls="sec_media"
                    data-section-title="{{ labels('admin_labels.media_manage', 'Media Manage') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.media_manage', 'Media Manage') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isMediaActive ? 'show' : '' }}" id="sec_media" data-default-open="{{ $isMediaActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/media') || Request::is('admin/media/*') ? 'active' : '' }}"
                                href="{{ route('admin.media') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.add_media', 'Add Media') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/storage_type') || Request::is('admin/storage_type/*') ? 'active' : '' }}"
                                href="{{ route('admin.storage_type') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.storage_type', 'Storage Type') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 10. Slider --}}
            <li class="sidebar-section {{ $isSliderActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isSliderActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_slider" role="button"
                    aria-expanded="{{ $isSliderActive ? 'true' : 'false' }}" aria-controls="sec_slider"
                    data-section-title="{{ labels('admin_labels.slider', 'Slider') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M7 2v20"/><path d="M17 2v20"/><rect width="10" height="14" x="7" y="5" rx="1"/><path d="M3 7v10"/><path d="M21 7v10"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.slider', 'Slider') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isSliderActive ? 'show' : '' }}" id="sec_slider" data-default-open="{{ $isSliderActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/sliders') || Request::is('admin/sliders/*') ? 'active' : '' }}"
                                href="{{ route('sliders.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.add_slider', 'Add Slider') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 11. Offers --}}
            <li class="sidebar-section {{ $isOffersActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isOffersActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_offers" role="button"
                    aria-expanded="{{ $isOffersActive ? 'true' : 'false' }}" aria-controls="sec_offers"
                    data-section-title="{{ labels('admin_labels.offers', 'Offers') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m15 9-6 6"/><path d="M9 9h.01"/><path d="M15 15h.01"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.offers', 'Offers') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isOffersActive ? 'show' : '' }}" id="sec_offers" data-default-open="{{ $isOffersActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/offers') ? 'active' : '' }}"
                                href="{{ route('offers.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.offers', 'Offers') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/offer_sliders') || Request::is('admin/offer_sliders/*') ? 'active' : '' }}"
                                href="{{ route('offer_sliders.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.offer_sliders', 'Offer Sliders') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/promo_codes') || Request::is('admin/promo_codes/*') ? 'active' : '' }}"
                                href="{{ route('promo_codes.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.promo_codes', 'Promo Codes') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 12. Support Tickets --}}
            <li class="sidebar-section {{ $isTicketsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isTicketsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_tickets" role="button"
                    aria-expanded="{{ $isTicketsActive ? 'true' : 'false' }}" aria-controls="sec_tickets"
                    data-section-title="{{ labels('admin_labels.support_tickets', 'Support Tickets') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.support_tickets', 'Support Tickets') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isTicketsActive ? 'show' : '' }}" id="sec_tickets" data-default-open="{{ $isTicketsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/tickets/ticket_types') || Request::is('admin/tickets/ticket_types*') ? 'active' : '' }}"
                                href="{{ route('ticket_types.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.ticket_types', 'Ticket Types') }}</span>
                            </a>
                        </li>
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view tickets'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/tickets') ? 'active' : '' }}"
                                    href="{{ route('admin.tickets.viewTickets') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.tickets', 'Tickets') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 13. Chat Manage --}}
            <li class="sidebar-section {{ $isChatActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isChatActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_chat" role="button"
                    aria-expanded="{{ $isChatActive ? 'true' : 'false' }}" aria-controls="sec_chat"
                    data-section-title="{{ labels('admin_labels.chat_manage', 'Chat Manage') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.chat_manage', 'Chat Manage') }}</span>
                    @if ($unread > 0)
                        <span class="flex-shrink-0 badge bg-danger rounded-pill ms-1">{{ $unread }}</span>
                        <span class="sidebar-unread-dot" aria-hidden="true"></span>
                    @endif
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isChatActive ? 'show' : '' }}" id="sec_chat" data-default-open="{{ $isChatActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/chat') || Request::is('admin/chat*') ? 'active' : '' }}"
                                href="{{ route('admin.chat.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.chats', 'Chats') }}</span>
                                @if ($unread > 0)
                                    <span class="flex-shrink-0 badge bg-danger rounded-pill ms-auto">{{ $unread }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 14. Featured Section --}}
            <li class="sidebar-section {{ $isFeaturedActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isFeaturedActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_featured" role="button"
                    aria-expanded="{{ $isFeaturedActive ? 'true' : 'false' }}" aria-controls="sec_featured"
                    data-section-title="{{ labels('admin_labels.featured_section', 'Featured Section') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.featured_section', 'Featured Section') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isFeaturedActive ? 'show' : '' }}" id="sec_featured" data-default-open="{{ $isFeaturedActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/feature_section') ? 'active' : '' }}"
                                href="{{ route('feature_section.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.featured', 'Featured') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/feature_section/section_order') ? 'active' : '' }}"
                                href="{{ route('feature_section.section_order') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.sections_order', 'Sections Order') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 15. Customers --}}
            <li class="sidebar-section {{ $isCustomersActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isCustomersActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_customers" role="button"
                    aria-expanded="{{ $isCustomersActive ? 'true' : 'false' }}" aria-controls="sec_customers"
                    data-section-title="{{ labels('admin_labels.customers', 'Customers') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.customers', 'Customers') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isCustomersActive ? 'show' : '' }}" id="sec_customers" data-default-open="{{ $isCustomersActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customers'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/customers') ? 'active' : '' }}"
                                    href="{{ route('admin.customers') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.view_customers', 'View Customers') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view address'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/customers/customers_addresses') ? 'active' : '' }}"
                                    href="{{ route('admin.customers.getCustomersAddresses') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.addresses', 'Addresses') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customer_transaction'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/customers/view_transactions') ? 'active' : '' }}"
                                    href="{{ route('admin.customers.viewTransactions') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.transactions', 'Transactions') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customer_wallet_transaction'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/customers/wallet_transaction') ? 'active' : '' }}"
                                    href="{{ route('admin.customers.walletTransaction') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.wallet_transactions', 'Wallet Transactions') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 16. Return Requests --}}
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view return_request'))
                <li class="sidebar-section {{ $isReturnActive ? 'is-active' : '' }}">
                    <a class="sidebar-section-trigger {{ $isReturnActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#sec_returns" role="button"
                        aria-expanded="{{ $isReturnActive ? 'true' : 'false' }}" aria-controls="sec_returns"
                        data-section-title="{{ labels('admin_labels.return_requests', 'Return Requests') }}">
                        <span class="nav-icon">
                            <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        </span>
                        <span class="nav-link-text">{{ labels('admin_labels.return_requests', 'Return Requests') }}</span>
                        {!! $chevronSvg !!}
                    </a>
                    <div class="collapse {{ $isReturnActive ? 'show' : '' }}" id="sec_returns" data-default-open="{{ $isReturnActive ? 'true' : 'false' }}">
                        <ul class="sidebar-sublist">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/return_request') || Request::is('admin/return_request/*') ? 'active' : '' }}"
                                    href="{{ route('admin.return_request.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.return_requests', 'Return Requests') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- 17. Delivery Boys --}}
            <li class="sidebar-section {{ $isDeliveryActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isDeliveryActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_delivery" role="button"
                    aria-expanded="{{ $isDeliveryActive ? 'true' : 'false' }}" aria-controls="sec_delivery"
                    data-section-title="{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isDeliveryActive ? 'show' : '' }}" id="sec_delivery" data-default-open="{{ $isDeliveryActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/delivery_boys') ? 'active' : '' }}"
                                href="{{ route('delivery_boys.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}</span>
                            </a>
                        </li>
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view delivery_boy_cash_collection'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/delivery_boys/manage_cash') || Request::is('admin/delivery_boys/manage_cash*') ? 'active' : '' }}"
                                    href="{{ route('admin.get_cash_collection.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.cash_collection', 'Cash Collection') }}</span>
                                </a>
                            </li>
                        @endif
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view fund_transfer'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/delivery_boys/fund_transfers') || Request::is('admin/delivery_boys/fund_transfers*') ? 'active' : '' }}"
                                    href="{{ route('admin.delivery_boys.fund_transfers.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.fund_transfer', 'Fund Transfer') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 18. Payment Request --}}
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view payment_request'))
                <li class="sidebar-section {{ $isPaymentReqActive ? 'is-active' : '' }}">
                    <a class="sidebar-section-trigger {{ $isPaymentReqActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#sec_payment_request" role="button"
                        aria-expanded="{{ $isPaymentReqActive ? 'true' : 'false' }}" aria-controls="sec_payment_request"
                        data-section-title="{{ labels('admin_labels.payment_request', 'Payment Request') }}">
                        <span class="nav-icon">
                            <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                        </span>
                        <span class="nav-link-text">{{ labels('admin_labels.payment_request', 'Payment Request') }}</span>
                        {!! $chevronSvg !!}
                    </a>
                    <div class="collapse {{ $isPaymentReqActive ? 'show' : '' }}" id="sec_payment_request" data-default-open="{{ $isPaymentReqActive ? 'true' : 'false' }}">
                        <ul class="sidebar-sublist">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/payment_request') || Request::is('admin/payment_request/*') ? 'active' : '' }}"
                                    href="{{ route('admin.payment_request.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.payment_request', 'Payment Request') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- 19. FAQs --}}
            <li class="sidebar-section {{ $isFaqActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isFaqActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_faqs" role="button"
                    aria-expanded="{{ $isFaqActive ? 'true' : 'false' }}" aria-controls="sec_faqs"
                    data-section-title="{{ labels('admin_labels.faqs', 'FAQs') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.faqs', 'FAQs') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isFaqActive ? 'show' : '' }}" id="sec_faqs" data-default-open="{{ $isFaqActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/faq') || Request::is('admin/faq/*') ? 'active' : '' }}"
                                href="{{ route('faqs.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.faqs', 'FAQs') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 20. Send Notification --}}
            <li class="sidebar-section {{ $isNotifActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isNotifActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_notifications" role="button"
                    aria-expanded="{{ $isNotifActive ? 'true' : 'false' }}" aria-controls="sec_notifications"
                    data-section-title="{{ labels('admin_labels.send_notification', 'Send Notification') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.send_notification', 'Send Notification') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isNotifActive ? 'show' : '' }}" id="sec_notifications" data-default-open="{{ $isNotifActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/send_notification') || Request::is('admin/send_notification/*') ? 'active' : '' }}"
                                href="{{ route('notifications.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.notification', 'Notification') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/send_seller_notification') || Request::is('admin/send_seller_notification/*') ? 'active' : '' }}"
                                href="{{ route('seller_notifications.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.seller_notification', 'Seller Notification') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/seller_email_notification') || Request::is('admin/seller_email_notification/*') ? 'active' : '' }}"
                                href="{{ route('seller_email_notifications.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.seller_email_notification', 'Seller Email Notification') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 21. Custom Message --}}
            <li class="sidebar-section {{ $isCustomMsgActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isCustomMsgActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_custom_msg" role="button"
                    aria-expanded="{{ $isCustomMsgActive ? 'true' : 'false' }}" aria-controls="sec_custom_msg"
                    data-section-title="{{ labels('admin_labels.custom_message', 'Custom Message') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M13 8H7"/><path d="M17 12H7"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.custom_message', 'Custom Message') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isCustomMsgActive ? 'show' : '' }}" id="sec_custom_msg" data-default-open="{{ $isCustomMsgActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/custom_message') || Request::is('admin/custom_message/*') ? 'active' : '' }}"
                                href="{{ route('admin.custom_message.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.add_custom_message', 'Add Custom Message') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 22. Location Management --}}
            <li class="sidebar-section {{ $isLocationActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isLocationActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_locations" role="button"
                    aria-expanded="{{ $isLocationActive ? 'true' : 'false' }}" aria-controls="sec_locations"
                    data-section-title="{{ labels('admin_labels.location_management', 'Location Management') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.location_management', 'Location Management') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isLocationActive ? 'show' : '' }}" id="sec_locations" data-default-open="{{ $isLocationActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/area/zipcodes') ? 'active' : '' }}"
                                href="{{ route('admin.display_zipcodes') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.zipcodes', 'Zipcodes') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/area/city') ? 'active' : '' }}"
                                href="{{ route('admin.display_city') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.city', 'City') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/pickup_location') ? 'active' : '' }}"
                                href="{{ route('admin.pickup_location.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.pickup_locations', 'Pickup Locations') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/zones') ? 'active' : '' }}"
                                href="{{ route('admin.zones.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.zones', 'Zones') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/area/location_bulk_upload') || Request::is('admin/area/location_bulk_upload/*') ? 'active' : '' }}"
                                href="{{ route('admin.location_bulk_upload.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 23. System Settings --}}
            <li class="sidebar-section {{ $isSysSettingsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isSysSettingsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_system_settings" role="button"
                    aria-expanded="{{ $isSysSettingsActive ? 'true' : 'false' }}" aria-controls="sec_system_settings"
                    data-section-title="{{ labels('admin_labels.system_settings', 'System Settings') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.system_settings', 'System Settings') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isSysSettingsActive ? 'show' : '' }}" id="sec_system_settings" data-default-open="{{ $isSysSettingsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/settings') ? 'active' : '' }}"
                                href="{{ route('settings.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.settings', 'Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 24. Web Settings --}}
            <li class="sidebar-section {{ $isWebSettingsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isWebSettingsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_web_settings" role="button"
                    aria-expanded="{{ $isWebSettingsActive ? 'true' : 'false' }}" aria-controls="sec_web_settings"
                    data-section-title="{{ labels('admin_labels.web_settings', 'Web Settings') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.web_settings', 'Web Settings') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isWebSettingsActive ? 'show' : '' }}" id="sec_web_settings" data-default-open="{{ $isWebSettingsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/web_settings/general_settings') || Request::is('admin/web_settings/general_settings*') ? 'active' : '' }}"
                                href="{{ route('general_settings') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.general_settings', 'General Settings') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/web_settings/pwa_settings') || Request::is('admin/web_settings/pwa_settings*') ? 'active' : '' }}"
                                href="{{ route('pwa_settings') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.pwa_settings', 'PWA Settings') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/web_settings/firebase') || Request::is('admin/web_settings/firebase*') ? 'active' : '' }}"
                                href="{{ route('firebase') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.firebase', 'Firebase') }}</span>
                            </a>
                        </li>
                        <li class="nav-item d-none">
                            <a class="nav-link {{ Request::is('admin/web_settings/theme') || Request::is('admin/web_settings/theme*') ? 'active' : '' }}"
                                href="{{ route('theme') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.themes', 'Themes') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/web_settings/language') || Request::is('admin/web_settings/language/*') ? 'active' : '' }}"
                                href="{{ route('web_language') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.languages', 'Languages') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 25. SEO Management --}}
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view seo'))
                <li class="sidebar-section {{ $isSeoActive ? 'is-active' : '' }}">
                    <a class="sidebar-section-trigger {{ $isSeoActive ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#sec_seo" role="button"
                        aria-expanded="{{ $isSeoActive ? 'true' : 'false' }}" aria-controls="sec_seo"
                        data-section-title="{{ labels('admin_labels.seo_management', 'SEO Management') }}">
                        <span class="nav-icon">
                            <svg class="muqtarin-lucide" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M11 8v6"/><path d="M8 11h6"/></svg>
                        </span>
                        <span class="nav-link-text">{{ labels('admin_labels.seo_management', 'SEO Management') }}</span>
                        {!! $chevronSvg !!}
                    </a>
                    <div class="collapse {{ $isSeoActive ? 'show' : '' }}" id="sec_seo" data-default-open="{{ $isSeoActive ? 'true' : 'false' }}">
                        <ul class="sidebar-sublist">
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/seo') || Request::is('admin/seo/*') ? 'active' : '' }}"
                                    href="{{ route('admin.seo.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.seo', 'SEO') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            {{-- 26. System Users --}}
            <li class="sidebar-section {{ $isSysUsersActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isSysUsersActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_system_users" role="button"
                    aria-expanded="{{ $isSysUsersActive ? 'true' : 'false' }}" aria-controls="sec_system_users"
                    data-section-title="{{ labels('admin_labels.system_users', 'System Users') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.system_users', 'System Users') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isSysUsersActive ? 'show' : '' }}" id="sec_system_users" data-default-open="{{ $isSysUsersActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/system_users') || Request::is('admin/system_users/*') ? 'active' : '' }}"
                                href="{{ route('admin.system_users.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.system_users', 'System Users') }}</span>
                            </a>
                        </li>
                        @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view system_user'))
                            <li class="nav-item">
                                <a class="nav-link {{ Request::is('admin/manage_system_users') || Request::is('admin/manage_system_users/*') ? 'active' : '' }}"
                                    href="{{ route('admin.manage_system_users') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.manage_system_users', 'Manage System Users') }}</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </li>

            {{-- 27. Language Settings --}}
            <li class="sidebar-section {{ $isLangActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isLangActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_languages" role="button"
                    aria-expanded="{{ $isLangActive ? 'true' : 'false' }}" aria-controls="sec_languages"
                    data-section-title="{{ labels('admin_labels.language_settings', 'Language Settings') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="m5 8 6 6"/><path d="m4 14 6-6 2-3"/><path d="M2 5h12"/><path d="M7 2h1"/><path d="m22 22-5-10-5 10"/><path d="M14 18h6"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.language_settings', 'Language Settings') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isLangActive ? 'show' : '' }}" id="sec_languages" data-default-open="{{ $isLangActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/settings/language') || Request::is('admin/settings/language/*') ? 'active' : '' }}"
                                href="{{ route('language.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.language', 'Language') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/settings/manage_language') || Request::is('admin/settings/manage_language/*') ? 'active' : '' }}"
                                href="{{ route('manage_language.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.manage_language', 'Manage Language') }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/language/bulk_translation_upload') || Request::is('admin/language/bulk_translation_upload/*') ? 'active' : '' }}"
                                href="{{ route('translation_bulk_upload.index') }}">
                                <span class="nav-link-text">{!! labels('admin_labels.bulk_upload', 'Multi Language Bulk<br>Import') !!}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- 28. Reports --}}
            <li class="sidebar-section {{ $isReportsActive ? 'is-active' : '' }}">
                <a class="sidebar-section-trigger {{ $isReportsActive ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#sec_reports" role="button"
                    aria-expanded="{{ $isReportsActive ? 'true' : 'false' }}" aria-controls="sec_reports"
                    data-section-title="{{ labels('admin_labels.reports', 'Reports') }}">
                    <span class="nav-icon">
                        <svg class="muqtarin-lucide" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                    </span>
                    <span class="nav-link-text">{{ labels('admin_labels.reports', 'Reports') }}</span>
                    {!! $chevronSvg !!}
                </a>
                <div class="collapse {{ $isReportsActive ? 'show' : '' }}" id="sec_reports" data-default-open="{{ $isReportsActive ? 'true' : 'false' }}">
                    <ul class="sidebar-sublist">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('admin/reports/') || Request::is('admin/settings/sales_reports/*') ? 'active' : '' }}"
                                href="{{ route('admin.sales_reports.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.sales_reports', 'Sales Reports') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navScroller = document.querySelector('.nav-scroller');
        const activeLink = document.querySelector('.navbar-vertical .nav-link.active');

        // Hide any section if role permissions hid all of its sub-items
        document.querySelectorAll('.navbar-vertical .sidebar-section').forEach(section => {
            const visibleItems = section.querySelectorAll('.sidebar-sublist li.nav-item:not(.d-none)');
            if (visibleItems.length === 0) {
                section.style.display = 'none';
            }
        });

        function saveScrollPosition() {
            if (navScroller) {
                sessionStorage.setItem('sidebarScrollPos', navScroller.scrollTop);
            }
        }

        function restoreScrollPosition() {
            if (navScroller) {
                const savedPos = sessionStorage.getItem('sidebarScrollPos');
                if (savedPos !== null) {
                    navScroller.scrollTop = parseInt(savedPos, 10);
                } else if (activeLink) {
                    setTimeout(() => {
                        activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
            }
        }

        if (navScroller) {
            navScroller.addEventListener('scroll', saveScrollPosition);
            window.addEventListener('beforeunload', saveScrollPosition);
        }

        restoreScrollPosition();

        const collapsibleMenus = document.querySelectorAll('.navbar-vertical [data-bs-toggle="collapse"]');
        collapsibleMenus.forEach(menu => {
            menu.addEventListener('click', function () {
                setTimeout(saveScrollPosition, 350);
            });
        });

        // Ensure active link's parent sections are expanded
        if (activeLink) {
            let parentCollapse = activeLink.closest('.collapse');
            while (parentCollapse) {
                parentCollapse.classList.add('show');
                const parentTrigger = document.querySelector(
                    `[data-bs-toggle="collapse"][href="#${parentCollapse.id}"], [data-bs-toggle="collapse"][data-bs-target="#${parentCollapse.id}"]`
                );
                if (parentTrigger) {
                    parentTrigger.classList.remove('collapsed');
                    parentTrigger.setAttribute('aria-expanded', 'true');
                }
                const parentSection = parentCollapse.closest('.sidebar-section');
                if (parentSection) {
                    parentSection.classList.add('is-active');
                }
                parentCollapse = parentCollapse.parentElement ? parentCollapse.parentElement.closest('.collapse') : null;
            }
        }

        // ── Collapsed Sidebar Hover Flyout Menu (Portal on document.body) ──
        const flyout = document.createElement('div');
        flyout.id = 'muqtarin-sidebar-flyout';
        flyout.className = 'muqtarin-sidebar-flyout';
        flyout.setAttribute('role', 'menu');
        flyout.setAttribute('aria-hidden', 'true');
        document.body.appendChild(flyout);

        let hideTimer = null;
        let currentTriggerItem = null;

        function isCollapsedDesktop() {
            return window.innerWidth >= 1200 && document.body.classList.contains('sidebar-collapsed');
        }

        function clearHideTimer() {
            if (hideTimer) {
                clearTimeout(hideTimer);
                hideTimer = null;
            }
        }

        function closeFlyout() {
            flyout.classList.remove('is-visible');
            flyout.setAttribute('aria-hidden', 'true');
            if (currentTriggerItem) {
                currentTriggerItem.classList.remove('flyout-open');
                currentTriggerItem = null;
            }
        }

        function scheduleCloseFlyout() {
            clearHideTimer();
            hideTimer = setTimeout(closeFlyout, 150);
        }

        function buildFlyoutContent(item) {
            const trigger = item.querySelector('.sidebar-section-trigger, .nav-link');
            if (!trigger) return false;

            const title = trigger.getAttribute('data-section-title')
                || (trigger.querySelector('.nav-link-text') ? trigger.querySelector('.nav-link-text').textContent.trim() : '');
            const iconWrap = trigger.querySelector('.nav-icon');
            const iconHtml = iconWrap ? iconWrap.innerHTML : '';

            let html = `<div class="muqtarin-flyout-header"><span class="nav-icon">${iconHtml}</span><span>${escapeHtml(title)}</span></div>`;
            html += '<ul class="muqtarin-flyout-list">';

            if (item.classList.contains('sidebar-direct')) {
                const href = trigger.getAttribute('href') || '#';
                const isActive = trigger.classList.contains('active') ? 'active' : '';
                html += `<li><a class="muqtarin-flyout-link ${isActive}" href="${href}"><span>${escapeHtml(title)}</span></a></li>`;
            } else {
                const sublist = item.querySelector('.sidebar-sublist');
                if (!sublist) return false;

                let hasAnyLink = false;
                Array.from(sublist.children).forEach(child => {
                    if (child.classList.contains('d-none')) return;

                    if (child.classList.contains('sidebar-subgroup-wrap')) {
                        const groupNav = child.querySelector('ul.nav');
                        const groupTitle = groupNav ? (groupNav.getAttribute('data-group-label') || '') : '';
                        if (groupTitle) {
                            html += `<li class="muqtarin-flyout-group-label">${escapeHtml(groupTitle)}</li>`;
                        }
                        if (groupNav) {
                            groupNav.querySelectorAll('li.nav-item:not(.d-none) > a.nav-link').forEach(link => {
                                hasAnyLink = true;
                                const href = link.getAttribute('href') || '#';
                                const labelEl = link.querySelector('.nav-link-text, .sidenav-normal') || link;
                                const labelText = labelEl.textContent.trim();
                                const isActive = link.classList.contains('active') || link.closest('li.nav-item')?.classList.contains('active') ? 'active' : '';
                                const badge = link.querySelector('.badge');
                                const badgeHtml = badge ? badge.outerHTML : '';
                                html += `<li><a class="muqtarin-flyout-link ${isActive}" href="${href}"><span>${escapeHtml(labelText)}</span>${badgeHtml}</a></li>`;
                            });
                        }
                    } else {
                        const link = child.querySelector('a.nav-link');
                        if (!link) return;
                        hasAnyLink = true;
                        const href = link.getAttribute('href') || '#';
                        const labelEl = link.querySelector('.nav-link-text, .sidenav-normal') || link;
                        const labelText = labelEl.textContent.trim();
                        const isActive = link.classList.contains('active') || child.classList.contains('active') ? 'active' : '';
                        const badge = link.querySelector('.badge');
                        const badgeHtml = badge ? badge.outerHTML : '';
                        html += `<li><a class="muqtarin-flyout-link ${isActive}" href="${href}"><span>${escapeHtml(labelText)}</span>${badgeHtml}</a></li>`;
                    }
                });

                if (!hasAnyLink) return false;
            }

            html += '</ul>';
            flyout.innerHTML = html;
            return true;
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>"']/g, function (m) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
            });
        }

        function openFlyoutFor(item) {
            if (!isCollapsedDesktop()) return;
            clearHideTimer();

            if (!buildFlyoutContent(item)) return;

            if (currentTriggerItem && currentTriggerItem !== item) {
                currentTriggerItem.classList.remove('flyout-open');
            }
            currentTriggerItem = item;
            currentTriggerItem.classList.add('flyout-open');

            const trigger = item.querySelector('.sidebar-section-trigger, .nav-link');
            const rect = (trigger || item).getBoundingClientRect();
            const isRtl = document.documentElement.getAttribute('dir') === 'rtl'
                || document.querySelector('.navbar-vertical')?.getAttribute('dir') === 'rtl';

            flyout.style.top = '0px';
            flyout.classList.add('is-visible');
            flyout.setAttribute('aria-hidden', 'false');

            if (isRtl) {
                flyout.style.left = 'auto';
                flyout.style.right = (window.innerWidth - rect.left + 8) + 'px';
            } else {
                flyout.style.right = 'auto';
                flyout.style.left = (rect.right + 8) + 'px';
            }

            const flyoutHeight = flyout.offsetHeight;
            let topPos = rect.top;
            if (topPos + flyoutHeight > window.innerHeight - 12) {
                topPos = Math.max(12, window.innerHeight - flyoutHeight - 12);
            }
            flyout.style.top = topPos + 'px';
        }

        document.querySelectorAll('.navbar-vertical .navbar-nav > li.sidebar-section, .navbar-vertical .navbar-nav > li.sidebar-direct').forEach(item => {
            item.addEventListener('mouseenter', function () {
                openFlyoutFor(item);
            });
            item.addEventListener('mouseleave', function () {
                scheduleCloseFlyout();
            });

            // When collapsed, clicking a section trigger opens/toggles the flyout cleanly without expanding accordion behind icon
            const trigger = item.querySelector('.sidebar-section-trigger');
            if (trigger) {
                trigger.addEventListener('click', function (e) {
                    if (isCollapsedDesktop()) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (currentTriggerItem === item && flyout.classList.contains('is-visible')) {
                            closeFlyout();
                        } else {
                            openFlyoutFor(item);
                        }
                    }
                });
            }
        });

        flyout.addEventListener('mouseenter', clearHideTimer);
        flyout.addEventListener('mouseleave', scheduleCloseFlyout);

        if (navScroller) {
            navScroller.addEventListener('scroll', function () {
                if (flyout.classList.contains('is-visible')) closeFlyout();
            });
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeFlyout();
        });

        window.addEventListener('resize', function () {
            if (!isCollapsedDesktop()) closeFlyout();
        });
    });
</script>