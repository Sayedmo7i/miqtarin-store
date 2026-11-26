@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.dashboard', 'Dashboard') }}
@endsection
@php
    use App\Models\Store;
    use App\Services\MediaService;
    use App\Services\OrderService;
    use App\Services\SettingService;
    use App\Services\TranslationService;

    $settings = app(SettingService::class)->getSettings('admin_preference', true);
    $adminPreferences = is_string($settings) ? json_decode($settings, true) : $settings;
    $showOrderNotifications =
        isset($adminPreferences['order_notification']) && $adminPreferences['order_notification'] == 1;

    $languageCode = app(TranslationService::class)->getLanguageCode();
    $activeStoreModel = !empty($store_id) ? Store::find($store_id) : null;
    $activeStoreName = $activeStoreModel
        ? app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store_id, $languageCode)
        : labels('admin_labels.dashboard', 'Dashboard');
    $activeStoreImage = $activeStoreModel && !empty($activeStoreModel->image)
        ? app(MediaService::class)->getMediaImageUrl($activeStoreModel->image, 'STORE_IMG_PATH')
        : asset('assets/brand/icon.jpg');

    $totalSteps = count($setupSteps);
    $completedCount = count(array_filter($completedSteps));
    $progressPercentage = $totalSteps > 0 ? round(($completedCount / $totalSteps) * 100) : 100;
    $allDone = $completedCount === $totalSteps;
    $firstIncompleteKey = array_search(false, $completedSteps, true);

    $stepRoutes = [
        'add_store' => route('admin.stores.index'),
        'add_category' => route('categories.index'),
        'add_city' => route('admin.display_city'),
        'add_zipcode' => route('admin.display_zipcodes'),
        'add_delivery_zone' => route('admin.zones.index'),
        'add_seller' => route('sellers.index'),
        'add_delivery_boy' => route('delivery_boys.index'),
        'add_product' => route('admin.products.index'),
    ];

    $orderService = app(OrderService::class);
    $maxOrderCount = (int) $orderService->ordersCount('', '', '', $store_id);
    $orderStages = [
        [
            'key' => 'received',
            'label' => labels('admin_labels.received', 'Received'),
            'icon' => 'bx bx-inbox',
            'tone' => 'is-received',
            'count' => (int) $orderService->ordersCount('received', '', '', $store_id),
        ],
        [
            'key' => 'processed',
            'label' => labels('admin_labels.processed', 'Processed'),
            'icon' => 'bx bx-loader-circle',
            'tone' => 'is-processed',
            'count' => (int) $orderService->ordersCount('processed', '', '', $store_id),
        ],
        [
            'key' => 'shipped',
            'label' => labels('admin_labels.shipped', 'Shipped'),
            'icon' => 'bx bx-package',
            'tone' => 'is-shipped',
            'count' => (int) $orderService->ordersCount('shipped', '', '', $store_id),
        ],
        [
            'key' => 'delivered',
            'label' => labels('admin_labels.delivered', 'Delivered'),
            'icon' => 'bx bx-badge-check',
            'tone' => 'is-delivered',
            'count' => (int) $orderService->ordersCount('delivered', '', '', $store_id),
        ],
        [
            'key' => 'cancelled',
            'label' => labels('admin_labels.cancelled', 'Cancelled'),
            'icon' => 'bx bx-x-circle',
            'tone' => 'is-cancelled',
            'count' => (int) $orderService->ordersCount('cancelled', '', '', $store_id),
        ],
        [
            'key' => 'returned',
            'label' => labels('admin_labels.returned', 'Returned'),
            'icon' => 'bx bx-undo',
            'tone' => 'is-returned',
            'count' => (int) $orderService->ordersCount('returned', '', '', $store_id),
        ],
    ];

    $formattedEarnings = number_format((float) ($total_earnings ?? 0), 2);
@endphp

@section('content')
    @include('Chatify::layouts.headLinks')

    <div class="muqtarin-dashboard">
        {{-- 1. Executive Welcome & Store Hero --}}
        <div class="muqtarin-dash-hero">
            <div class="muqtarin-dash-hero-brand">
                <img src="{{ $activeStoreImage }}"
                     onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                     alt="{{ $activeStoreName }}"
                     class="muqtarin-dash-store-avatar">
                <div>
                    <div class="muqtarin-dash-hero-eyebrow">
                        <span class="muqtarin-pulse-dot"></span>
                        <span>{{ labels('admin_labels.active_store', 'Active Store') }}: {{ $activeStoreName }}</span>
                    </div>
                    <h2>{{ labels('admin_labels.dashboard', 'Dashboard') }}</h2>
                    <p>{{ labels('admin_labels.drive_success_for_you_and_your_sellers', 'Drive Success For You and Your Sellers') }}</p>
                </div>
            </div>

            <div class="muqtarin-dash-actions">
                <a href="{{ route('admin.products.index') }}" class="muqtarin-hero-cta">
                    <i class="bx bx-plus-circle"></i>
                    <span>{{ labels('admin_labels.add_product', 'Add Product') }}</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="muqtarin-hero-ghost">
                    <i class="bx bx-receipt"></i>
                    <span>{{ labels('admin_labels.manage_orders', 'Manage Orders') }}</span>
                </a>
                <a href="{{ route('admin.stores.manage_store') }}" class="muqtarin-hero-ghost">
                    <i class="bx bx-store-alt"></i>
                    <span>{{ labels('admin_labels.manage_stores', 'Manage Stores') }}</span>
                </a>
            </div>
        </div>

        {{-- 2. Store Setup Checklist (Interactive & Localized) --}}
        @if (!$allDone)
            <div class="muqtarin-setup-card">
                <div class="muqtarin-setup-head">
                    <div class="muqtarin-setup-title">
                        <span class="muqtarin-setup-icon"><i class="bx bx-rocket"></i></span>
                        <div>
                            <h5 class="mb-0">{{ labels('admin_labels.store_setup_checklist', 'Store Setup Checklist') }}</h5>
                            <small class="text-muted">
                                {{ $completedCount }} / {{ $totalSteps }} {{ labels('admin_labels.steps_completed', 'steps completed') }}
                            </small>
                        </div>
                    </div>
                    <div class="muqtarin-setup-meta">
                        <span class="muqtarin-setup-pill">{{ $progressPercentage }}%</span>
                    </div>
                </div>

                <div class="muqtarin-setup-track">
                    <div class="muqtarin-setup-fill" style="width: {{ $progressPercentage }}%;"></div>
                </div>

                <div class="muqtarin-setup-grid">
                    @foreach ($setupSteps as $stepKey => $stepLabel)
                        @php
                            $done = $completedSteps[$stepKey] ?? false;
                            $active = !$done && $stepKey === $firstIncompleteKey;
                            $stepUrl = $stepRoutes[$stepKey] ?? '#';
                            $localizedStep = labels('admin_labels.' . $stepKey, is_array($stepLabel) ? ($stepLabel['label'] ?? $stepKey) : $stepLabel);
                        @endphp
                        <a href="{{ $stepUrl }}"
                           class="muqtarin-setup-item {{ $done ? 'is-done' : '' }} {{ $active ? 'is-active' : '' }}">
                            <span class="muqtarin-setup-num">
                                @if ($done)
                                    <i class="bx bx-check"></i>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </span>
                            <span class="muqtarin-setup-label" title="{{ $localizedStep }}">{{ $localizedStep }}</span>
                            <i class="bx bx-chevron-right muqtarin-setup-arrow"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif (session('show_done_message'))
            <div class="muqtarin-setup-card">
                <div class="d-flex align-items-center gap-3">
                    <span class="muqtarin-setup-icon"><i class="bx bx-party"></i></span>
                    <div>
                        <h5 class="mb-1">{{ labels('admin_labels.you_are_all_set', 'You are all set!') }}</h5>
                        <p class="mb-0 text-muted">{{ labels('admin_labels.all_setup_done_message', 'All setup steps are completed. Your system is ready to go.') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- 3. Bento KPI Row (Spotlight Cards) --}}
        <div class="muqtarin-kpi-grid">
            <a href="{{ route('sellers.index') }}" class="muqtarin-kpi-card">
                <div class="muqtarin-kpi-top">
                    <span class="muqtarin-kpi-label home_page_statitics">{{ labels('admin_labels.sellers', 'Sellers') }}</span>
                    <span class="muqtarin-kpi-icon tone-pine"><i class="bx bx-store"></i></span>
                </div>
                <div class="muqtarin-kpi-bottom">
                    <span class="muqtarin-kpi-value">{{ isset($total_seller) && !empty($total_seller) ? number_format($total_seller) : 0 }}</span>
                    <span class="muqtarin-kpi-go"><i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="muqtarin-kpi-card">
                <div class="muqtarin-kpi-top">
                    <span class="muqtarin-kpi-label home_page_statitics">{{ labels('admin_labels.orders', 'Orders') }}</span>
                    <span class="muqtarin-kpi-icon tone-info"><i class="bx bx-shopping-bag"></i></span>
                </div>
                <div class="muqtarin-kpi-bottom">
                    <span class="muqtarin-kpi-value">{{ isset($order_counter['total_orders']) && !empty($order_counter['total_orders']) ? number_format($order_counter['total_orders']) : 0 }}</span>
                    <span class="muqtarin-kpi-go"><i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>

            <a href="{{ route('admin.products.manage_product') }}" class="muqtarin-kpi-card">
                <div class="muqtarin-kpi-top">
                    <span class="muqtarin-kpi-label home_page_statitics">{{ labels('admin_labels.products', 'Products') }}</span>
                    <span class="muqtarin-kpi-icon tone-emerald"><i class="bx bx-box"></i></span>
                </div>
                <div class="muqtarin-kpi-bottom">
                    <span class="muqtarin-kpi-value">{{ isset($total_products) && !empty($total_products) ? number_format($total_products) : 0 }}</span>
                    <span class="muqtarin-kpi-go"><i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="muqtarin-kpi-card">
                <div class="muqtarin-kpi-top">
                    <span class="muqtarin-kpi-label home_page_statitics">{{ labels('admin_labels.total_earnings', 'Total Earnings') }}</span>
                    <span class="muqtarin-kpi-icon tone-gold"><i class="bx bx-wallet"></i></span>
                </div>
                <div class="muqtarin-kpi-bottom">
                    <span class="muqtarin-kpi-value">{{ $currency }} {{ $formattedEarnings }}</span>
                    <span class="muqtarin-kpi-go"><i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>
        </div>

        {{-- Optional Order Notifications Banner --}}
        @if ($showOrderNotifications)
            @php
                $recentOrders = $orderService->getRecentOrdersForNotifications($store_id);
            @endphp
            <div class="card muqtarin-dash-card">
                <div class="card-body">
                    <div class="muqtarin-card-head">
                        <div class="muqtarin-card-title-group">
                            <span class="muqtarin-card-title-icon"><i class="bx bx-bell"></i></span>
                            <h4>{{ labels('admin_labels.order_notifications', 'Order Notifications') }}</h4>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="muqtarin-view-link">
                            <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                        </a>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($recentOrders as $order)
                            <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0">
                                <div>
                                    <strong>{{ labels('admin_labels.order_id', 'Order ID') }}: #{{ $order->id }}</strong>
                                    <p class="mb-0 text-muted small">
                                        {{ labels('admin_labels.status', 'Status') }}: {{ ucfirst($order->active_status) }} ·
                                        {{ labels('admin_labels.date', 'Date') }}: {{ $order->date_added }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.orders.view', $order->id) }}" class="btn btn-sm btn-primary">
                                    {{ labels('admin_labels.view', 'View') }}
                                </a>
                            </div>
                        @empty
                            <div class="muqtarin-empty-state" style="min-height: 100px;">
                                <i class="bx bx-bell-off"></i>
                                <p>{{ labels('admin_labels.no_notifications', 'No new order notifications') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif

        {{-- 4. Primary Analytics Row: Revenue Analytics + Orders Overview --}}
        <div class="row g-3 align-items-stretch">
            <div class="col-12 col-xxl-8 overview-statistic">
                <div class="card revenue_card muqtarin-dash-card">
                    <div class="card-body">
                        <div class="muqtarin-card-head chart-card-header">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-line-chart"></i></span>
                                <h4>{{ labels('admin_labels.revenue_analytics', 'Revenue Analytics') }}</h4>
                            </div>
                            <ul class="nav nav-pills chart-action sale-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#Daily" role="tab">{{ labels('admin_labels.today', 'Today') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#Weekly" role="tab">{{ labels('admin_labels.weekly', 'Weekly') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#Monthly" role="tab">{{ labels('admin_labels.monthly', 'Monthly') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div id="muqtarin_revenue_chart" class="admin_statistic_chart"></div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-4">
                <div class="card muqtarin-dash-card">
                    <div class="card-body">
                        <div class="muqtarin-card-head">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-package"></i></span>
                                <h4>{{ labels('admin_labels.orders_overview', 'Orders Overview') }}</h4>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" class="muqtarin-view-link">
                                <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                            </a>
                        </div>

                        <div class="muqtarin-pipeline-list">
                            @foreach ($orderStages as $stage)
                                @php
                                    $stageWidth = $maxOrderCount > 0 ? round(($stage['count'] / $maxOrderCount) * 100, 1) : 0;
                                @endphp
                                <div class="muqtarin-pipeline-row">
                                    <div class="muqtarin-pipeline-icon {{ $stage['tone'] }}">
                                        <i class="{{ $stage['icon'] }}"></i>
                                    </div>
                                    <div class="muqtarin-pipeline-meta">
                                        <div class="muqtarin-pipeline-labels">
                                            <span>{{ $stage['label'] }}</span>
                                            <span class="muqtarin-pipeline-count total-order">{{ $stage['count'] }}</span>
                                        </div>
                                        <div class="muqtarin-pipeline-track" role="progressbar"
                                             aria-valuenow="{{ $stage['count'] }}" aria-valuemin="0" aria-valuemax="{{ max($maxOrderCount, 1) }}">
                                            <div class="muqtarin-pipeline-bar {{ $stage['tone'] }}" style="width: {{ $stageWidth }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Secondary Intelligence Row: Customer Statistics + Top Sellers + New Messages --}}
        <div class="row g-3 align-items-stretch">
            <div class="col-12 col-lg-6 col-xxl-4">
                <div class="card muqtarin-dash-card">
                    <div class="card-body customer_statistics_card">
                        <div class="muqtarin-card-head">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-group"></i></span>
                                <h4>{{ labels('admin_labels.customer_statistics', 'Customer Statistics') }}</h4>
                            </div>
                            <a class="muqtarin-view-link view_all" href="{{ route('admin.customers') }}">
                                <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                            </a>
                        </div>
                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <div id="muqtarin_customer_chart" class="customer_statistics w-100"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6 col-xxl-4">
                <div class="card muqtarin-dash-card">
                    <div class="card-body">
                        <div class="muqtarin-card-head">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-trophy"></i></span>
                                <h4>{{ labels('admin_labels.top_sellers', 'Top Sellers') }}</h4>
                            </div>
                            <a class="muqtarin-view-link view_all" href="{{ route('sellers.index') }}">
                                <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                            </a>
                        </div>

                        <div class="d-flex flex-column flex-grow-1">
                            @forelse ($top_sellers as $item)
                                @php
                                    $sellerAvatar = !empty($item['logo'])
                                        ? route('admin.dynamic_image', [
                                            'url' => app(MediaService::class)->getMediaImageUrl($item['logo'], 'SELLER_IMG_PATH'),
                                            'width' => 80,
                                            'quality' => 90,
                                        ])
                                        : asset('assets/brand/icon.jpg');
                                @endphp
                                <div class="muqtarin-seller-row">
                                    <span class="muqtarin-seller-rank {{ $loop->first ? 'rank-1' : '' }}">#{{ $loop->iteration }}</span>
                                    <img src="{{ $sellerAvatar }}"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                                         alt="{{ $item['store_name'] }}"
                                         class="muqtarin-seller-avatar">
                                    <div class="muqtarin-seller-info">
                                        <p class="name">{{ $item['seller_name'] }}</p>
                                        <p class="store">{{ $item['store_name'] }}</p>
                                    </div>
                                    <div class="muqtarin-seller-metrics">
                                        <p class="amount">{{ $currency }} {{ number_format((float) $item['total_sales']) }}</p>
                                        <p class="comm">{{ $item['total_commission'] }} {{ labels('admin_labels.sales_count_label', 'Sales') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="muqtarin-empty-state">
                                    <i class="bx bx-store-alt"></i>
                                    <p>{{ labels('admin_labels.no_data_found_text', 'No data found') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-4 overview-statistic">
                <div class="chart-card contact-list muqtarin-dash-card">
                    <div class="muqtarin-card-head">
                        <div class="muqtarin-card-title-group">
                            <span class="muqtarin-card-title-icon"><i class="bx bx-message-dots"></i></span>
                            <h4>{{ labels('admin_labels.new_messages', 'New Messages') }}</h4>
                        </div>
                        <a class="muqtarin-view-link view_all" href="{{ route('admin.chat.index') }}">
                            <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                        </a>
                    </div>
                    <div class="listOfContacts"></div>
                </div>
            </div>
        </div>

        {{-- 6. Operations Row: Recent Tickets & Recent Order Items --}}
        <div class="row g-3 align-items-stretch">
            <div class="col-12 col-xxl-5">
                <div class="card muqtarin-dash-card">
                    <div class="card-body">
                        <div class="muqtarin-card-head">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-support"></i></span>
                                <h4>{{ labels('admin_labels.recent_tickets', 'Recent Tickets') }}</h4>
                            </div>
                            <a class="muqtarin-view-link view_all" href="{{ route('admin.tickets.viewTickets') }}">
                                <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                            </a>
                        </div>
                        <div class="table-responsive">
                            <table class='table' id="admin_ticket_table" data-toggle="table"
                                data-loading-template="loadingTemplate"
                                data-url="{{ route('admin.tickets.getTicketList') }}" data-click-to-select="true"
                                data-side-pagination="server" data-pagination="true"
                                data-page-list="[5, 10, 20, 50, 100, 200]" data-search="false" data-show-columns="false"
                                data-show-refresh="false" data-trim-on-search="false" data-sort-name="id"
                                data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="false" data-maintain-selected="true"
                                data-export-types='["txt","excel"]' data-query-params="queryParams">
                                <thead>
                                    <tr>
                                        <th data-field="ticket_type" data-sortable="false">
                                            {{ labels('admin_labels.ticket_type', 'Ticket Type') }}
                                        </th>
                                        <th data-field="subject" data-sortable="false">
                                            {{ labels('admin_labels.subject', 'Subject') }}
                                        </th>
                                        <th data-field="status" data-sortable="false">
                                            {{ labels('admin_labels.status', 'Status') }}
                                        </th>
                                        <th data-field="date_created" data-sortable="false">
                                            {{ labels('admin_labels.date_created', 'Date Created') }}
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-7">
                <div class="card muqtarin-dash-card">
                    <div class="card-body">
                        <div class="muqtarin-card-head">
                            <div class="muqtarin-card-title-group">
                                <span class="muqtarin-card-title-icon"><i class="bx bx-receipt"></i></span>
                                <h4>{{ labels('admin_labels.order_items', 'Order Items') }}</h4>
                            </div>
                            <a class="muqtarin-view-link view_all" href="{{ route('admin.orders.index') }}">
                                <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                            </a>
                        </div>
                        <div id="order_items_table">
                            <div class="table-responsive">
                                <table id="seller_order_item_table" data-toggle="table"
                                    data-loading-template="loadingTemplate"
                                    data-url="{{ route('admin.orders.item_list') }}"
                                    data-click-to-select="true" data-side-pagination="server"
                                    data-pagination="true" data-page-list="[5, 10, 20, 50, 100, 200]"
                                    data-search="false" data-show-columns="false"
                                    data-show-refresh="false" data-trim-on-search="false"
                                    data-sort-name="o.id" data-sort-order="desc"
                                    data-mobile-responsive="true" data-toolbar=""
                                    data-show-export="false" data-maintain-selected="true"
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable='true' data-footer-formatter="totalFormatter">
                                                {{ labels('admin_labels.order_id', 'Order ID') }}
                                            </th>
                                            <th data-field="username" data-sortable='false'>
                                                {{ labels('admin_labels.user_name', 'User Name') }}
                                            </th>
                                            <th data-field="payment_method" data-visible="true">
                                                {{ labels('admin_labels.payment_method', 'Payment Method') }}
                                            </th>
                                            <th data-field="active_status" data-sortable='false' data-visible='true'>
                                                {{ labels('admin_labels.active_status', 'Active Status') }}
                                            </th>
                                            <th data-field="date_added">
                                                {{ labels('admin_labels.order_date', 'Order Date') }}
                                            </th>
                                            <th data-field="sub_total" data-sortable='false' data-visible="true">
                                                {{ labels('admin_labels.total', 'Total') }} ({{ $currency }})
                                            </th>
                                            <th data-field="operate" data-sortable='false'>
                                                {{ labels('admin_labels.action', 'Action') }}
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 7. Missing Language Labels Summary Accordion --}}
        @if (isset($missing_labels_summary) && count($missing_labels_summary) > 0)
            <div class="card muqtarin-dash-card">
                <div class="card-body">
                    <div class="muqtarin-card-head">
                        <div class="muqtarin-card-title-group">
                            <span class="muqtarin-card-title-icon"><i class="bx bx-globe"></i></span>
                            <h4>{{ labels('admin_labels.all_missing_labels', 'All Missing Labels') }}</h4>
                        </div>
                        <a href="{{ route('language.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-language me-1"></i>{{ labels('admin_labels.manage_languages', 'Manage Languages') }}
                        </a>
                    </div>
                    <div class="accordion" id="missingLabelsAccordion">
                        @php $accordionIndex = 0; @endphp
                        @foreach ($missing_labels_summary as $language)
                            @foreach ($language['files'] as $file)
                                @if ($file['missing_labels_count'] > 0)
                                    @php
                                        $accordionIndex++;
                                        $itemId = 'missing-' . $accordionIndex;
                                    @endphp
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-{{ $itemId }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse-{{ $itemId }}"
                                                aria-expanded="false"
                                                aria-controls="collapse-{{ $itemId }}">
                                                <span class="me-2 text-uppercase badge bg-info">{{ $file['file_type'] }}</span>
                                                <strong class="me-2">{{ ucfirst($language['language_name']) }}</strong>
                                                <small class="text-muted">({{ $language['language_code'] }})</small>
                                                <span class="badge bg-danger ms-auto">{{ $file['missing_labels_count'] }}</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $itemId }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading-{{ $itemId }}"
                                            data-bs-parent="#missingLabelsAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                                                    <span>
                                                        <strong>{{ labels('admin_labels.total_missing', 'Total Missing') }}:</strong>
                                                        <span class="badge bg-danger">{{ $file['missing_labels_count'] }}</span>
                                                    </span>
                                                    <span>
                                                        <strong>{{ labels('admin_labels.last_updated', 'Last Updated') }}:</strong>
                                                        {{ $file['updated_at'] ? \Carbon\Carbon::parse($file['updated_at'])->format('M d, Y H:i') : labels('admin_labels.never', 'Never') }}
                                                    </span>
                                                    <a href="{{ route('language.index') }}?lang={{ $language['language_code'] }}&type={{ $file['file_type'] }}"
                                                        class="btn btn-sm btn-outline-primary ms-auto">
                                                        <i class="fas fa-edit me-1"></i>{{ labels('admin_labels.edit', 'Edit') }}
                                                    </a>
                                                </div>
                                                <div class="row">
                                                    @foreach ($file['missing_labels'] as $idx => $label)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-danger me-2">{{ $idx + 1 }}</span>
                                                                <code class="text-break" style="font-size: 0.9em;">{{ $label }}</code>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal: Missing Labels --}}
    <div class="modal fade" id="missingLabelsModal" tabindex="-1" aria-labelledby="missingLabelsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="missingLabelsModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        {{ labels('admin_labels.missing_labels', 'Missing Labels') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>{{ labels('admin_labels.language', 'Language') }}:</strong>
                        <span id="modal_language_name"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ labels('admin_labels.file_type', 'File Type') }}:</strong>
                        <span class="badge bg-info" id="modal_file_type"></span>
                    </div>
                    <div class="mb-3">
                        <strong>{{ labels('admin_labels.total_missing', 'Total Missing') }}:</strong>
                        <span class="badge bg-danger" id="modal_total_count"></span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <input type="text" id="search_missing_labels" class="form-control"
                            placeholder="{{ labels('admin_labels.search_labels', 'Search labels...') }}">
                    </div>
                    <div id="missing_labels_list" style="max-height: 400px; overflow-y: auto;" class="border p-3 rounded"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ labels('admin_labels.close', 'Close') }}</button>
                    <button type="button" class="btn btn-primary" id="copy_all_labels_btn">
                        <i class="fas fa-copy me-1"></i>{{ labels('admin_labels.copy_all', 'Copy All') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Order Tracking --}}
    <div class="modal fade" id="edit_order_tracking_modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="user_name">
                        {{ labels('admin_labels.order_tracking', 'Order Tracking') }}
                    </h5>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <form class="form-horizontal" id="order_tracking_form"
                    action="{{ route('admin.orders.update_order_tracking') }}" method="POST"
                    enctype="multipart/form-data">
                    @method('POST')
                    @csrf
                    <input type="hidden" name="order_id" id="order_id">
                    <input type="hidden" name="order_item_id" id="order_item_id">
                    <input type="hidden" name="seller_id" id="seller_id">
                    <input type="hidden" id="edit_zipcode_id" name="edit_zipcode_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="courier_agency">{{ labels('admin_labels.courier_agency', 'Courier Agency') }}</label>
                            <input type="text" class="form-control" name="courier_agency" id="courier_agency"
                                placeholder="{{ labels('admin_labels.placeholder_courier_agency', 'Courier Agency') }}" />
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="tracking_id">{{ labels('admin_labels.tracking_id', 'Tracking ID') }}</label>
                            <input type="text" class="form-control" name="tracking_id" id="tracking_id"
                                placeholder="{{ labels('admin_labels.placeholder_tracking_id', 'Tracking Id') }}" />
                        </div>
                        <div class="form-group">
                            <label class="mb-2 mt-2" for="url">{{ labels('admin_labels.url', 'URL') }}</label>
                            <input type="text" class="form-control" name="url" id="url"
                                placeholder="{{ labels('admin_labels.url_text', 'URL') }}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn mx-2 reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                            <button type="submit" class="btn btn-primary" id="submit_btn">{{ labels('admin_labels.save', 'Save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // 21st.dev Spotlight hover tracking on KPI cards
            document.querySelectorAll('.muqtarin-kpi-card').forEach(function(card) {
                card.addEventListener('mousemove', function(e) {
                    var rect = card.getBoundingClientRect();
                    card.style.setProperty('--mx', (e.clientX - rect.left) + 'px');
                    card.style.setProperty('--my', (e.clientY - rect.top) + 'px');
                });
            });

            setTimeout(function() {
                var isDarkTheme = function() {
                    return document.documentElement.getAttribute('data-theme') === 'dark';
                };

                var labelSales = {!! json_encode(labels('admin_labels.sales', 'Sales'), JSON_UNESCAPED_UNICODE) !!};
                var labelRevenue = {!! json_encode(labels('admin_labels.revenue', 'Revenue'), JSON_UNESCAPED_UNICODE) !!};
                var labelCommission = {!! json_encode(labels('admin_labels.commission', 'Commission'), JSON_UNESCAPED_UNICODE) !!};
                var labelNew = {!! json_encode(labels('admin_labels.new', 'New'), JSON_UNESCAPED_UNICODE) !!};
                var labelActive = {!! json_encode(labels('admin_labels.active', 'Active'), JSON_UNESCAPED_UNICODE) !!};
                var labelDeactive = {!! json_encode(labels('admin_labels.deactive', 'Deactive'), JSON_UNESCAPED_UNICODE) !!};
                var labelTotal = {!! json_encode(labels('admin_labels.total', 'Total'), JSON_UNESCAPED_UNICODE) !!};
                var currencySymbol = {!! json_encode($currency, JSON_UNESCAPED_UNICODE) !!};

                const data = {
                    Monthly: {
                        series: [
                            { name: labelSales, data: {!! json_encode($sales[0]['total_sales']) !!} },
                            { name: labelRevenue, data: {!! json_encode($sales[0]['total_revenue']) !!} },
                            { name: labelCommission, data: {!! json_encode($sales[0]['total_commission']) !!} }
                        ],
                        categories: {!! json_encode($sales[0]['month_name'], JSON_UNESCAPED_UNICODE) !!}
                    },
                    Weekly: {
                        series: [
                            { name: labelSales, data: {!! json_encode($sales[1]['total_sales']) !!} },
                            { name: labelRevenue, data: {!! json_encode($sales[1]['total_revenue']) !!} },
                            { name: labelCommission, data: {!! json_encode($sales[1]['total_commission']) !!} }
                        ],
                        categories: {!! json_encode($sales[1]['day'], JSON_UNESCAPED_UNICODE) !!}
                    },
                    Daily: {
                        series: [
                            { name: labelSales, data: {!! json_encode($sales[2]['total_sales']) !!} },
                            { name: labelRevenue, data: {!! json_encode($sales[2]['total_revenue']) !!} },
                            { name: labelCommission, data: {!! json_encode($sales[2]['total_commission']) !!} }
                        ],
                        categories: {!! json_encode($sales[2]['day'], JSON_UNESCAPED_UNICODE) !!}
                    }
                };

                let chartData = data['Daily'];
                var dark = isDarkTheme();

                var adminOptions = {
                    series: chartData.series,
                    colors: ['#22C55E', '#3B82F6', '#F6C667'],
                    chart: {
                        height: 335,
                        type: 'area',
                        fontFamily: "'Tajawal', 'Inter', sans-serif",
                        foreColor: dark ? '#94A3B8' : '#64748B',
                        toolbar: { show: false }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2.5 },
                    grid: {
                        borderColor: dark ? '#30363D' : '#E2E8F0',
                        strokeDashArray: 4
                    },
                    xaxis: {
                        categories: chartData.categories,
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return currencySymbol + ' ' + (value / 1000).toFixed(1) + 'k';
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.04,
                            stops: [0, 95, 100]
                        }
                    },
                    tooltip: {
                        theme: dark ? 'dark' : 'light',
                        y: {
                            formatter: function(val) {
                                return currencySymbol + ' ' + val;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left'
                    }
                };

                var adminChartEl = document.querySelector(".admin_statistic_chart");
                var adminChart = adminChartEl ? new ApexCharts(adminChartEl, adminOptions) : null;
                if (adminChart) {
                    adminChart.render();
                }

                $(".overview-statistic .chart-action li a").on("click", function() {
                    $(".chart-action li a").removeClass('active');
                    $(this).addClass('active');

                    var key = $(this).attr("href").replace('#', '');
                    chartData = data[key];
                    if (adminChart && chartData) {
                        adminChart.updateOptions({
                            series: chartData.series,
                            xaxis: { categories: chartData.categories }
                        });
                    }
                    return false;
                });

                var user_data = {!! json_encode($user_counter) !!};
                var customerOptions = {
                    series: [
                        Number(user_data.current_month_users || 0),
                        Number(user_data.active_user || 0),
                        Number(user_data.inactive_user || 0)
                    ],
                    labels: [labelNew, labelActive, labelDeactive],
                    chart: {
                        width: '100%',
                        height: 310,
                        type: 'donut',
                        fontFamily: "'Tajawal', 'Inter', sans-serif",
                        foreColor: dark ? '#94A3B8' : '#64748B'
                    },
                    stroke: {
                        colors: [dark ? '#161B22' : '#FFFFFF'],
                        width: 2
                    },
                    legend: {
                        position: "bottom"
                    },
                    colors: ['#2563EB', '#22C55E', '#F6C667'],
                    dataLabels: { enabled: false },
                    tooltip: {
                        theme: dark ? 'dark' : 'light'
                    },
                    plotOptions: {
                        pie: {
                            expandOnClick: false,
                            donut: {
                                size: '68%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '15px',
                                        offsetY: -4
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 700,
                                        color: dark ? '#F1F5F9' : '#1F2937',
                                        offsetY: 4,
                                        formatter: function(val) {
                                            return val;
                                        }
                                    },
                                    total: {
                                        show: true,
                                        label: labelTotal,
                                        color: dark ? '#4ADE80' : '#0F4C3A',
                                        formatter: function() {
                                            return user_data.total_users || 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                };

                var customerChartEl = document.querySelector(".customer_statistics");
                var customerChart = customerChartEl ? new ApexCharts(customerChartEl, customerOptions) : null;
                if (customerChart) {
                    customerChart.render();
                }

                // Live-sync ApexCharts palette when switching Dark/Light mode
                var themeObserver = new MutationObserver(function() {
                    var nowDark = isDarkTheme();
                    if (adminChart) {
                        adminChart.updateOptions({
                            chart: { foreColor: nowDark ? '#94A3B8' : '#64748B' },
                            grid: { borderColor: nowDark ? '#30363D' : '#E2E8F0' },
                            tooltip: { theme: nowDark ? 'dark' : 'light' }
                        }, false, false);
                    }
                    if (customerChart) {
                        customerChart.updateOptions({
                            chart: { foreColor: nowDark ? '#94A3B8' : '#64748B' },
                            stroke: { colors: [nowDark ? '#161B22' : '#FFFFFF'] },
                            tooltip: { theme: nowDark ? 'dark' : 'light' },
                            plotOptions: {
                                pie: {
                                    donut: {
                                        labels: {
                                            value: { color: nowDark ? '#F1F5F9' : '#1F2937' },
                                            total: { color: nowDark ? '#4ADE80' : '#0F4C3A' }
                                        }
                                    }
                                }
                            }
                        }, false, false);
                    }
                });
                themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-theme']
                });
            }, 150);
        });
    </script>

    @include('Chatify::layouts.modals')
    @include('Chatify::layouts.footerLinks')
@endsection
