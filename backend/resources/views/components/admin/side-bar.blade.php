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

        <!-- code for menu search -->
        <div class="px-2 sidebar-search-wrap">
            <input type="text" class="form-control menuSearch"
                placeholder="{{ labels('admin_labels.search', 'Search') }}">
        </div>

        <ul class="navbar-nav" id="menuList">
            <li class="sidebar-title"><i class='bx bx-tachometer'></i>
                <span>{{ labels('admin_labels.dashboard', 'Dashboard') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/home') || Request::is('admin/home/*') ? 'active' : '' }}"
                    href="{{ route('admin.home') }}" title="{{ labels('admin_labels.dashboard', 'Dashboard') }}">
                    <i class='bx bx-home-alt nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.dashboard', 'Dashboard') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-store-alt'></i>
                <span>{{ labels('admin_labels.stores', 'Stores') }}</span>
            </li>
            @if (($settings->store_mode ?? '') != 'single')
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/stores') || Request::is('admin/stores') ? 'active' : '' }}"
                        href="{{ route('admin.stores.index') }}" title="{{ labels('admin_labels.add_store', 'Add Store') }}">
                        <i class='bx bx-plus-circle nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.add_store', 'Add Store') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view store'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/stores/manage_store') || Request::is('admin/stores/manage_store*') ? 'active' : '' }}"
                        href="{{ route('admin.stores.manage_store') }}" title="{{ labels('admin_labels.manage_stores', 'Manage Stores') }}">
                        <i class='bx bx-store nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.manage_stores', 'Manage Stores') }}</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/store/custom_fields') || Request::is('admin/store/custom_fields') ? 'active' : '' }}"
                    href="{{ route('admin.custom_fields.index') }}" title="{{ labels('admin_labels.custom_fields', 'Custom Fields') }}">
                    <i class='bx bx-customize nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.custom_fields', 'Custom Fields') }}</span>
                </a>
            </li>

            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view orders'))
                <li class="sidebar-title"><i class='bx bx-card'></i>
                    <span>{{ labels('admin_labels.manage', 'Manage') }}</span>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#order_dropdown"
                        class="nav-link {{ Request::is('admin/orders') || Request::is('admin/orders*') || Request::is('admin/order_items') ? 'active' : 'collapsed' }}"
                        aria-controls="order_dropdown" role="button" aria-expanded="false"
                        title="{{ labels('admin_labels.orders_manage', 'Orders Manage') }}">
                        <i class='bx bx-shopping-bag nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.orders_manage', 'Orders Manage') }}</span>
                        <i class="fas fa-angle-down"></i>
                    </a>
                    <div class="collapse {{ Request::is('admin/orders') || Request::is('admin/orders*') || Request::is('admin/order_items') ? 'show' : '' }}"
                        id="order_dropdown">
                        <ul class="nav">
                            <li class="nav-item {{ Request::is('admin/orders') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.orders', 'Orders') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/order_items') || Request::is('admin/order_items*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.order_items.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.order_items', 'Order Items') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('admin/orders/order_tracking') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('admin.orders.order_tracking') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.order_tracking', 'Order Tracking') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#category_dropdown"
                    class="nav-link {{ Request::is('admin/categories') || Request::is('admin/categories/*') ? 'active' : 'collapsed' }}"
                    aria-controls="category_dropdown" role="button" aria-expanded="false"
                    title="{{ labels('admin_labels.categories', 'Categories') }}">
                    <i class='bx bx-category nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.categories', 'Categories') }}</span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <div class="collapse {{ Request::is('admin/categories') || Request::is('admin/categories/*') ? 'show' : '' }}"
                    id="category_dropdown">
                    <ul class="nav">
                        <li class="nav-item {{ Request::is('admin/categories') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('categories.index') }}">
                                <span class="nav-link-text">{{ labels('admin_labels.categories', 'Categories') }}</span>
                            </a>
                        </li>
                        @if ($user_role == 'super_admin' || $user->hasPermissionTo('view category_order'))
                            <li class="nav-item {{ Request::is('admin/categories/category_order') || Request::is('admin/categories/category_order/*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('category_order.index') }}">
                                    <span class="nav-link-text">{{ labels('admin_labels.categories_order', 'Categories Order') }}</span>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item {{ Request::is('admin/categories/category_slider') || Request::is('admin/categories/category_slider/*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('category_slider.index') }}">
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
                        href="{{ route('admin.manage_stock.index') }}" title="{{ labels('admin_labels.stock_manage', 'Stock Manage') }}">
                        <i class='bx bx-archive nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.stock_manage', 'Stock Manage') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view combo_stock'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/manage_combo_stock') || Request::is('admin/manage_combo_stock/*') ? 'active' : '' }}"
                        href="{{ route('admin.manage_combo_stock.index') }}" title="{{ labels('admin_labels.combo_stock_manage', 'Combo Stock Manage') }}">
                        <i class='bx bx-layer nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.combo_stock_manage', 'Combo Stock Manage') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-purchase-tag-alt'></i>
                <span>{{ labels('admin_labels.brand', 'Brand') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/brands') ? 'active' : '' }}"
                    href="{{ route('brands.index') }}" title="{{ labels('admin_labels.brand', 'Brand') }}">
                    <i class='bx bx-bookmark nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.brand', 'Brand') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/brands/bulk_upload') || Request::is('admin/brands/bulk_upload/*') ? 'active' : '' }}"
                    href="{{ route('brands.bulk_upload') }}" title="{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}">
                    <i class='bx bx-upload nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-user-check'></i>
                <span>{{ labels('admin_labels.sellers', 'Sellers') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/seller/*') || Request::is('admin/seller/create') ? 'active' : '' }}"
                    href="{{ route('admin.sellers.create') }}" title="{{ labels('admin_labels.add_sellers', 'Add Sellers') }}">
                    <i class='bx bx-user-plus nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.add_sellers', 'Add Sellers') }}</span>
                </a>
            </li>
            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view seller'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/sellers') ? 'active' : '' }}"
                        href="{{ route('sellers.index') }}" title="{{ labels('admin_labels.sellers', 'Sellers') }}">
                        <i class='bx bx-briefcase-alt-2 nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.sellers', 'Sellers') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view seller_wallet_transaction'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/sellers/seller_wallet_transaction') || Request::is('admin/sellers/seller_wallet_transaction/*') ? 'active' : '' }}"
                        href="{{ route('admin.sellers.sellerWallet') }}" title="{{ labels('admin_labels.wallet_transactions', 'Wallet Transaction') }}">
                        <i class='bx bx-wallet nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.wallet_transactions', 'Wallet Transaction') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-cart-alt'></i>
                <span>{{ labels('admin_labels.products', 'Products') }}</span>
            </li>
            @if ($user_role == 'super_admin' || $user->hasPermissionTo('view tax'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/taxes') || Request::is('admin/taxes/*') ? 'active' : '' }}"
                        href="{{ route('taxes.index') }}" title="{{ labels('admin_labels.tax', 'Tax') }}">
                        <i class='bx bx-receipt nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.tax', 'Tax') }}</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/attributes') || Request::is('admin/attributes*') ? 'active' : '' }}"
                    href="{{ route('admin.attributes.index') }}" title="{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}">
                    <i class='bx bx-slider-alt nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#products_dropdown"
                    class="nav-link {{ Request::is('admin/products') || Request::is('admin/products/*') || Request::is('admin/product_faqs') ? 'active' : 'collapsed' }}"
                    aria-controls="products_dropdown" role="button" aria-expanded="false"
                    title="{{ labels('admin_labels.products_manage', 'Products Manage') }}">
                    <i class='bx bx-box nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.products_manage', 'Products Manage') }}</span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <div class="collapse {{ Request::is('admin/products') || Request::is('admin/products/*') || Request::is('admin/product_faqs') ? 'show' : '' }}"
                    id="products_dropdown">
                    <ul class="nav">
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

            <li class="sidebar-title"><i class='bx bx-package'></i>
                <span>{{ labels('admin_labels.combo_products_manage', 'Combo Products Manage') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/combo_product_attributes') || Request::is('admin/combo_product_attributes/*') ? 'active' : '' }}"
                    href="{{ route('admin.combo_product_attributes.index') }}" title="{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}">
                    <i class='bx bx-list-check nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.attributes_manage', 'Attributes Manage') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#combo_products_dropdown"
                    class="nav-link {{ Request::is('admin/combo_products') || Request::is('admin/combo_products*') || Request::is('admin/combo_product_faqs') ? 'active' : 'collapsed' }}"
                    aria-controls="combo_products_dropdown" role="button" aria-expanded="false"
                    title="{{ labels('admin_labels.products_manage', 'Products Manage') }}">
                    <i class='bx bx-package nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.products_manage', 'Products Manage') }}</span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <div class="collapse {{ Request::is('admin/combo_products') || Request::is('admin/combo_products*') || Request::is('admin/combo_product_faqs') ? 'show' : '' }}"
                    id="combo_products_dropdown">
                    <ul class="nav">
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

            <li class="sidebar-title"><i class='bx bx-share-alt'></i>
                <span>{{ labels('admin_labels.affiliate', 'Affiliate System') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/manage_affiliate_users') || Request::is('admin/add_affiliate_user') || Request::is('admin/affiliate_users*') ? 'active' : '' }}"
                    href="{{ route('admin.affiliate.manage_user') }}" title="{{ labels('admin_labels.affiliate_users', 'Affiliate Users') }}">
                    <i class='bx bx-network-chart nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.affiliate_users', 'Affiliate Users') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/affiliate_settings') ? 'active' : '' }}"
                    href="{{ route('admin.affiliate.settings') }}" title="{{ labels('admin_labels.settings', 'Settings') }}">
                    <i class='bx bx-cog nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.settings', 'Settings') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/affiliate/reports') ? 'active' : '' }}"
                    href="{{ route('admin.affiliate.reports') }}" title="{{ labels('admin_labels.reports', 'Reports') }}">
                    <i class='bx bx-bar-chart-alt-2 nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.reports', 'Reports') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/affiliate_policies') ? 'active' : '' }}"
                    href="{{ route('admin.affiliate.policies') }}" title="{{ labels('admin_labels.policies', 'Policies') }}">
                    <i class='bx bx-file nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.policies', 'Policies') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-news'></i>
                <span>{{ labels('admin_labels.blogs', 'Blogs') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/blogs') ? 'active' : '' }}"
                    href="{{ route('admin.blogs.index') }}" title="{{ labels('admin_labels.blog_categories', 'Blog Categories') }}">
                    <i class='bx bx-folder nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.blog_categories', 'Blog Categories') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/manage_blogs') || Request::is('admin/manage_blogs/*') ? 'active' : '' }}"
                    href="{{ route('manage_blogs.index') }}" title="{{ labels('admin_labels.create_blog', 'Create Blog') }}">
                    <i class='bx bx-edit nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.create_blog', 'Create Blog') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-image-add'></i>
                <span>{{ labels('admin_labels.media_manage', 'Media Manage') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/media') || Request::is('admin/media/*') ? 'active' : '' }}"
                    href="{{ route('admin.media') }}" title="{{ labels('admin_labels.add_media', 'Add Media') }}">
                    <i class='bx bx-images nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.add_media', 'Add Media') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/storage_type') || Request::is('admin/storage_type/*') ? 'active' : '' }}"
                    href="{{ route('admin.storage_type') }}" title="{{ labels('admin_labels.storage_type', 'Storage Type') }}">
                    <i class='bx bx-hdd nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.storage_type', 'Storage Type') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-carousel'></i>
                <span>{{ labels('admin_labels.slider', 'Slider') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/sliders') || Request::is('admin/sliders/*') ? 'active' : '' }}"
                    href="{{ route('sliders.index') }}" title="{{ labels('admin_labels.add_slider', 'Add Slider') }}">
                    <i class='bx bx-slideshow nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.add_slider', 'Add Slider') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-gift'></i>
                <span>{{ labels('admin_labels.offers', 'Offers') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/offers') ? 'active' : '' }}"
                    href="{{ route('offers.index') }}" title="{{ labels('admin_labels.offers', 'Offers') }}">
                    <i class='bx bx-purchase-tag nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.offers', 'Offers') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/offer_sliders') || Request::is('admin/offer_sliders/*') ? 'active' : '' }}"
                    href="{{ route('offer_sliders.index') }}" title="{{ labels('admin_labels.offer_sliders', 'Offer Sliders') }}">
                    <i class='bx bx-carousel nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.offer_sliders', 'Offer Sliders') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/promo_codes') || Request::is('admin/promo_codes/*') ? 'active' : '' }}"
                    href="{{ route('promo_codes.index') }}" title="{{ labels('admin_labels.promo_codes', 'Promo Codes') }}">
                    <i class='bx bx-badge-check nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.promo_codes', 'Promo Codes') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-support'></i>
                <span>{{ labels('admin_labels.support_tickets', 'Support Tickets') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/tickets/ticket_types') || Request::is('admin/tickets/ticket_types*') ? 'active' : '' }}"
                    href="{{ route('ticket_types.index') }}" title="{{ labels('admin_labels.ticket_types', 'Ticket Types') }}">
                    <i class='bx bx-tag nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.ticket_types', 'Ticket Types') }}</span>
                </a>
            </li>
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view tickets'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/tickets') ? 'active' : '' }}"
                        href="{{ route('admin.tickets.viewTickets') }}" title="{{ labels('admin_labels.tickets', 'Tickets') }}">
                        <i class='bx bx-support nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.tickets', 'Tickets') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-chat'></i>
                <span>{{ labels('admin_labels.chat_manage', 'Chat Manage') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/chat') || Request::is('admin/chat*') ? 'active' : '' }}"
                    href="{{ route('admin.chat.index') }}" title="{{ labels('admin_labels.chats', 'Chats') }}">
                    <i class='bx bx-message-rounded-dots nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.chats', 'Chats') }}</span>
                    @if ($unread > 0)
                        <span class="flex-shrink-0 badge bg-danger rounded-pill ms-auto">{{ $unread }}</span>
                    @endif
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-star'></i>
                <span>{{ labels('admin_labels.featured_section', 'Featured Section') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/feature_section') ? 'active' : '' }}"
                    href="{{ route('feature_section.index') }}" title="{{ labels('admin_labels.featured', 'Featured') }}">
                    <i class='bx bx-layout nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.featured', 'Featured') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/feature_section/section_order') ? 'active' : '' }}"
                    href="{{ route('feature_section.section_order') }}" title="{{ labels('admin_labels.sections_order', 'Sections Order') }}">
                    <i class='bx bx-sort-alt-2 nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.sections_order', 'Sections Order') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-user'></i>
                <span>{{ labels('admin_labels.customers', 'Customers') }}</span>
            </li>
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customers'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/customers') ? 'active' : '' }}"
                        href="{{ route('admin.customers') }}" title="{{ labels('admin_labels.view_customers', 'View Customers') }}">
                        <i class='bx bx-group nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.view_customers', 'View Customers') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view address'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/customers/customers_addresses') ? 'active' : '' }}"
                        href="{{ route('admin.customers.getCustomersAddresses') }}" title="{{ labels('admin_labels.addresses', 'Addresses') }}">
                        <i class='bx bx-map-pin nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.addresses', 'Addresses') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customer_transaction'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/customers/view_transactions') ? 'active' : '' }}"
                        href="{{ route('admin.customers.viewTransactions') }}" title="{{ labels('admin_labels.transactions', 'Transactions') }}">
                        <i class='bx bx-transfer nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.transactions', 'Transactions') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view customer_wallet_transaction'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/customers/wallet_transaction') ? 'active' : '' }}"
                        href="{{ route('admin.customers.walletTransaction') }}" title="{{ labels('admin_labels.wallet_transactions', 'Wallet Transactions') }}">
                        <i class='bx bx-credit-card nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.wallet_transactions', 'Wallet Transactions') }}</span>
                    </a>
                </li>
            @endif

            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view return_request'))
                <li class="sidebar-title"><i class='bx bx-revision'></i>
                    <span>{{ labels('admin_labels.return_requests', 'Return Requests') }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/return_request') || Request::is('admin/return_request/*') ? 'active' : '' }}"
                        href="{{ route('admin.return_request.index') }}" title="{{ labels('admin_labels.return_requests', 'Return Requests') }}">
                        <i class='bx bx-undo nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.return_requests', 'Return Requests') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-cycling'></i>
                <span>{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/delivery_boys') ? 'active' : '' }}"
                    href="{{ route('delivery_boys.index') }}" title="{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}">
                    <i class='bx bx-cycling nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}</span>
                </a>
            </li>
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view delivery_boy_cash_collection'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/delivery_boys/manage_cash') || Request::is('admin/delivery_boys/manage_cash*') ? 'active' : '' }}"
                        href="{{ route('admin.get_cash_collection.index') }}" title="{{ labels('admin_labels.cash_collection', 'Cash Collection') }}">
                        <i class='bx bx-money nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.cash_collection', 'Cash Collection') }}</span>
                    </a>
                </li>
            @endif
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view fund_transfer'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/delivery_boys/fund_transfers') || Request::is('admin/delivery_boys/fund_transfers*') ? 'active' : '' }}"
                        href="{{ route('admin.delivery_boys.fund_transfers.index') }}" title="{{ labels('admin_labels.fund_transfer', 'Fund Transfer') }}">
                        <i class='bx bx-send nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.fund_transfer', 'Fund Transfer') }}</span>
                    </a>
                </li>
            @endif

            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view payment_request'))
                <li class="sidebar-title"><i class='bx bx-wallet-alt'></i>
                    <span>{{ labels('admin_labels.payment_request', 'Payment Request') }}</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/payment_request') || Request::is('admin/payment_request/*') ? 'active' : '' }}"
                        href="{{ route('admin.payment_request.index') }}" title="{{ labels('admin_labels.payment_request', 'Payment Request') }}">
                        <i class='bx bx-wallet-alt nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.payment_request', 'Payment Request') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-help-circle'></i>
                <span>{{ labels('admin_labels.faqs', 'FAQs') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/faq') || Request::is('admin/faq/*') ? 'active' : '' }}"
                    href="{{ route('faqs.index') }}" title="{{ labels('admin_labels.faqs', 'FAQs') }}">
                    <i class='bx bx-help-circle nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.faqs', 'FAQs') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-send'></i>
                <span>{{ labels('admin_labels.send_notification', 'Send Notification') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/send_notification') || Request::is('admin/send_notification/*') ? 'active' : '' }}"
                    href="{{ route('notifications.index') }}" title="{{ labels('admin_labels.notification', 'Notification') }}">
                    <i class='bx bx-bell nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.notification', 'Notification') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/send_seller_notification') || Request::is('admin/send_seller_notification/*') ? 'active' : '' }}"
                    href="{{ route('seller_notifications.index') }}" title="{{ labels('admin_labels.seller_notification', 'Seller Notification') }}">
                    <i class='bx bx-bell-plus nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.seller_notification', 'Seller Notification') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/seller_email_notification') || Request::is('admin/seller_email_notification/*') ? 'active' : '' }}"
                    href="{{ route('seller_email_notifications.index') }}" title="{{ labels('admin_labels.seller_email_notification', 'Seller Email Notification') }}">
                    <i class='bx bx-envelope nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.seller_email_notification', 'Seller Email Notification') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-message-dots'></i>
                <span>{{ labels('admin_labels.custom_message', 'Custom Message') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/custom_message') || Request::is('admin/custom_message/*') ? 'active' : '' }}"
                    href="{{ route('admin.custom_message.index') }}" title="{{ labels('admin_labels.add_custom_message', 'Add Custom Message') }}">
                    <i class='bx bx-message-alt-edit nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.add_custom_message', 'Add Custom Message') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-map'></i>
                <span>{{ labels('admin_labels.location_management', 'Location Management') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/area/zipcodes') ? 'active' : '' }}"
                    href="{{ route('admin.display_zipcodes') }}" title="{{ labels('admin_labels.zipcodes', 'Zipcodes') }}">
                    <i class='bx bx-map-alt nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.zipcodes', 'Zipcodes') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/area/city') ? 'active' : '' }}"
                    href="{{ route('admin.display_city') }}" title="{{ labels('admin_labels.city', 'City') }}">
                    <i class='bx bx-buildings nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.city', 'City') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/pickup_location') ? 'active' : '' }}"
                    href="{{ route('admin.pickup_location.index') }}" title="{{ labels('admin_labels.pickup_locations', 'Pickup Locations') }}">
                    <i class='bx bx-location-plus nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.pickup_locations', 'Pickup Locations') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/zones') ? 'active' : '' }}"
                    href="{{ route('admin.zones.index') }}" title="{{ labels('admin_labels.zones', 'Zones') }}">
                    <i class='bx bx-area nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.zones', 'Zones') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/area/location_bulk_upload') || Request::is('admin/area/location_bulk_upload/*') ? 'active' : '' }}"
                    href="{{ route('admin.location_bulk_upload.index') }}" title="{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}">
                    <i class='bx bx-cloud-upload nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.bulk_upload', 'Bulk Upload') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-cog'></i>
                <span>{{ labels('admin_labels.system_settings', 'System Settings') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/settings') ? 'active' : '' }}"
                    href="{{ route('settings.index') }}" title="{{ labels('admin_labels.settings', 'Settings') }}">
                    <i class='bx bx-slider nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.settings', 'Settings') }}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-globe'></i>
                <span>{{ labels('admin_labels.web_settings', 'Web Settings') }}</span>
            </li>
            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#web_setting_dropdown"
                    class="nav-link {{ Request::is('admin/web_settings*') ? 'active' : 'collapsed' }}"
                    aria-controls="web_setting_dropdown" role="button" aria-expanded="false"
                    title="{{ labels('admin_labels.web_settings', 'Web Settings') }}">
                    <i class='bx bx-globe nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.web_settings', 'Web Settings') }}</span>
                    <i class="fas fa-angle-down"></i>
                </a>
                <div class="collapse {{ Request::is('admin/web_settings*') ? 'show' : '' }}" id="web_setting_dropdown">
                    <ul class="nav">
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

            <li class="sidebar-title"><i class='bx bx-search-alt'></i>
                <span>{{ labels('admin_labels.seo_management', 'SEO Management') }}</span>
            </li>
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view seo'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/seo') || Request::is('admin/seo/*') ? 'active' : '' }}"
                        href="{{ route('admin.seo.index') }}" title="{{ labels('admin_labels.seo', 'SEO') }}">
                        <i class='bx bx-line-chart nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.seo', 'SEO') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-group'></i>
                <span>{{ labels('admin_labels.system_users', 'System Users') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/system_users') || Request::is('admin/system_users/*') ? 'active' : '' }}"
                    href="{{ route('admin.system_users.index') }}" title="{{ labels('admin_labels.system_users', 'System Users') }}">
                    <i class='bx bx-shield-quarter nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.system_users', 'System Users') }}</span>
                </a>
            </li>
            @if ($user_role == 'super_admin' || $logged_in_user->hasPermissionTo('view system_user'))
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('admin/manage_system_users') || Request::is('admin/manage_system_users/*') ? 'active' : '' }}"
                        href="{{ route('admin.manage_system_users') }}" title="{{ labels('admin_labels.manage_system_users', 'Manage System Users') }}">
                        <i class='bx bx-user-pin nav-icon'></i>
                        <span class="nav-link-text">{{ labels('admin_labels.manage_system_users', 'Manage System Users') }}</span>
                    </a>
                </li>
            @endif

            <li class="sidebar-title"><i class='bx bx-text'></i>
                <span>{{ labels('admin_labels.language_settings', 'Language Settings') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/settings/language') || Request::is('admin/settings/language/*') ? 'active' : '' }}"
                    href="{{ route('language.index') }}" title="{{ labels('admin_labels.language', 'Language') }}">
                    <i class='bx bx-font nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.language', 'Language') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/settings/manage_language') || Request::is('admin/settings/manage_language/*') ? 'active' : '' }}"
                    href="{{ route('manage_language.index') }}" title="{{ labels('admin_labels.manage_language', 'Manage Language') }}">
                    <i class='bx bx-world nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.manage_language', 'Manage Language') }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/language/bulk_translation_upload') || Request::is('admin/language/bulk_translation_upload/*') ? 'active' : '' }}"
                    href="{{ route('translation_bulk_upload.index') }}" title="{{ labels('admin_labels.bulk_upload', 'Multi Language Bulk Import') }}">
                    <i class='bx bx-import nav-icon'></i>
                    <span class="nav-link-text">{!! labels('admin_labels.bulk_upload', 'Multi Language Bulk<br>Import') !!}</span>
                </a>
            </li>

            <li class="sidebar-title"><i class='bx bx-bar-chart'></i>
                <span>{{ labels('admin_labels.reports', 'Reports') }}</span>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('admin/reports/') || Request::is('admin/settings/sales_reports/*') ? 'active' : '' }}"
                    href="{{ route('admin.sales_reports.index') }}" title="{{ labels('admin_labels.sales_reports', 'Sales Reports') }}">
                    <i class='bx bx-pie-chart-alt-2 nav-icon'></i>
                    <span class="nav-link-text">{{ labels('admin_labels.sales_reports', 'Sales Reports') }}</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navScroller = document.querySelector('.nav-scroller');
        const activeLink = document.querySelector('.nav-link.active');

        function saveScrollPosition() {
            if (navScroller) {
                sessionStorage.setItem('sidebarScrollPos', navScroller.scrollTop);
            }
        }

        function restoreScrollPosition() {
            if (navScroller) {
                const savedPos = sessionStorage.getItem('sidebarScrollPos');
                if (savedPos !== null) {
                    navScroller.scrollTop = parseInt(savedPos);
                } else if (activeLink) {
                    setTimeout(() => {
                        activeLink.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 100);
                }
            }
        }

        if (navScroller) {
            navScroller.addEventListener('scroll', saveScrollPosition);
            window.addEventListener('beforeunload', saveScrollPosition);
        }

        restoreScrollPosition();

        const collapsibleMenus = document.querySelectorAll('[data-bs-toggle="collapse"]');
        collapsibleMenus.forEach(menu => {
            menu.addEventListener('click', function () {
                setTimeout(saveScrollPosition, 350);
            });
        });

        if (activeLink) {
            const parentCollapse = activeLink.closest('.collapse');
            if (parentCollapse) {
                const parentTrigger = document.querySelector(
                    `[data-bs-toggle="collapse"][href="#${parentCollapse.id}"], [data-bs-toggle="collapse"][data-bs-target="#${parentCollapse.id}"]`);
                if (parentTrigger) {
                    parentCollapse.classList.add('show');
                    parentTrigger.classList.remove('collapsed');
                    parentTrigger.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
</script>