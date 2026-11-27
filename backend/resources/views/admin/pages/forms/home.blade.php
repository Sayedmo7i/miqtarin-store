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
    $chatPrefixUrl = url(config('chatify.routes.prefix', 'chatify'));
@endphp

@section('content')
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

        {{-- 1B. Operational Telemetry Strip --}}
        <div class="muqtarin-telemetry-strip">
            <a href="{{ route('admin.stores.manage_store') }}" class="muqtarin-telemetry-chip">
                <div class="muqtarin-telemetry-left">
                    <i class="bx bx-store-alt"></i>
                    <span>{{ labels('admin_labels.total_stores', 'Total Stores') }}</span>
                </div>
                <span class="muqtarin-telemetry-val">{{ number_format((int) ($total_store ?? 0)) }}</span>
            </a>
            <a href="{{ route('categories.index') }}" class="muqtarin-telemetry-chip">
                <div class="muqtarin-telemetry-left">
                    <i class="bx bx-category"></i>
                    <span>{{ labels('admin_labels.categories', 'Categories') }}</span>
                </div>
                <span class="muqtarin-telemetry-val">{{ number_format((int) ($total_categories ?? 0)) }}</span>
            </a>
            <a href="{{ route('delivery_boys.index') }}" class="muqtarin-telemetry-chip">
                <div class="muqtarin-telemetry-left">
                    <i class="bx bx-cycling"></i>
                    <span>{{ labels('admin_labels.delivery_boys', 'Delivery Boys') }}</span>
                </div>
                <span class="muqtarin-telemetry-val">{{ number_format((int) ($delivery_boy_counter ?? 0)) }}</span>
            </a>
            <a href="{{ route('admin.tickets.viewTickets') }}" class="muqtarin-telemetry-chip">
                <div class="muqtarin-telemetry-left">
                    <i class="bx bx-support"></i>
                    <span>{{ labels('admin_labels.open_tickets', 'Open Tickets') }}</span>
                </div>
                <span class="muqtarin-telemetry-val">{{ number_format((int) ($pending_tickets ?? 0)) }}</span>
            </a>
        </div>

        {{-- 2. Store Setup Checklist (Collapsible, Interactive & Localized) --}}
        @if (!$allDone)
            <div class="muqtarin-setup-card" id="muqtarinSetupCard">
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
                        <button type="button" class="muqtarin-setup-toggle" id="muqtarinSetupToggle"
                            data-collapse-text="{{ labels('admin_labels.collapse', 'Collapse') }}"
                            data-expand-text="{{ labels('admin_labels.expand', 'Expand') }}">
                            <i class="bx bx-chevron-up"></i>
                            <span>{{ labels('admin_labels.collapse', 'Collapse') }}</span>
                        </button>
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
                            <ul class="nav chart-action sale-tabs" role="tablist">
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

                        {{-- Period Summary Stat Pills (synced with Today / Weekly / Monthly tabs) --}}
                        <div class="muqtarin-chart-kpis">
                            <div class="muqtarin-chart-kpi">
                                <span class="muqtarin-chart-kpi-label">
                                    <span class="muqtarin-chart-kpi-dot" style="background: #22C55E;"></span>
                                    {{ labels('admin_labels.total_sales_qty', 'Units Sold') }}
                                </span>
                                <span class="muqtarin-chart-kpi-value" id="muqtarinKpiSales">0</span>
                            </div>
                            <div class="muqtarin-chart-kpi">
                                <span class="muqtarin-chart-kpi-label">
                                    <span class="muqtarin-chart-kpi-dot" style="background: #3B82F6;"></span>
                                    {{ labels('admin_labels.total_revenue', 'Total Revenue') }}
                                </span>
                                <span class="muqtarin-chart-kpi-value" id="muqtarinKpiRevenue">{{ $currency }} 0</span>
                            </div>
                            <div class="muqtarin-chart-kpi">
                                <span class="muqtarin-chart-kpi-label">
                                    <span class="muqtarin-chart-kpi-dot" style="background: #F6C667;"></span>
                                    {{ labels('admin_labels.total_commission', 'Admin Commission') }}
                                </span>
                                <span class="muqtarin-chart-kpi-value" id="muqtarinKpiCommission">{{ $currency }} 0</span>
                            </div>
                        </div>

                        <div id="muqtarin_revenue_chart" class="admin_statistic_chart"></div>

                        <div id="muqtarinRevenueZeroNote" class="muqtarin-chart-zero-note d-none">
                            <i class="bx bx-info-circle"></i>
                            <span>{{ labels('admin_labels.no_revenue_yet', 'No revenue recorded for this period yet') }}</span>
                        </div>
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
                            <div class="d-flex align-items-center gap-2">
                                <span class="muqtarin-setup-pill">{{ $maxOrderCount }}</span>
                                <a href="{{ route('admin.orders.index') }}" class="muqtarin-view-link">
                                    <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="muqtarin-pipeline-list">
                            @foreach ($orderStages as $stage)
                                @php
                                    $stageWidth = $maxOrderCount > 0 ? round(($stage['count'] / $maxOrderCount) * 100, 1) : 0;
                                @endphp
                                <a href="{{ route('admin.orders.index', ['order_status' => $stage['key']]) }}" class="muqtarin-pipeline-row">
                                    <div class="muqtarin-pipeline-icon {{ $stage['tone'] }}">
                                        <i class="{{ $stage['icon'] }}"></i>
                                    </div>
                                    <div class="muqtarin-pipeline-meta">
                                        <div class="muqtarin-pipeline-labels">
                                            <span>{{ $stage['label'] }}</span>
                                            <span>
                                                <span class="muqtarin-pipeline-pct">{{ $stageWidth }}%</span>
                                                <span class="muqtarin-pipeline-count total-order">{{ $stage['count'] }}</span>
                                            </span>
                                        </div>
                                        <div class="muqtarin-pipeline-track" role="progressbar"
                                             aria-valuenow="{{ $stage['count'] }}" aria-valuemin="0" aria-valuemax="{{ max($maxOrderCount, 1) }}">
                                            <div class="muqtarin-pipeline-bar {{ $stage['tone'] }}" style="width: {{ $stageWidth }}%"></div>
                                        </div>
                                    </div>
                                </a>
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
                            <div class="d-flex align-items-center gap-2">
                                <span class="muqtarin-setup-pill">{{ (int) ($user_counter['total_users'] ?? 0) }}</span>
                                <a class="muqtarin-view-link view_all" href="{{ route('admin.customers') }}">
                                    <span>{{ labels('admin_labels.view_all', 'View All') }}</span>
                                </a>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center align-items-center flex-grow-1">
                            <div id="muqtarin_customer_chart" class="customer_statistics w-100"></div>
                        </div>

                        <div class="muqtarin-customer-breakdown">
                            <div class="muqtarin-cust-pill">
                                <span class="muqtarin-cust-pill-top">
                                    <span class="muqtarin-cust-dot" style="background: #2563EB;"></span>
                                    {{ labels('admin_labels.new_customers', 'New (Month)') }}
                                </span>
                                <strong>{{ number_format((int) ($user_counter['current_month_users'] ?? 0)) }}</strong>
                            </div>
                            <div class="muqtarin-cust-pill">
                                <span class="muqtarin-cust-pill-top">
                                    <span class="muqtarin-cust-dot" style="background: #22C55E;"></span>
                                    {{ labels('admin_labels.active_customers', 'Active') }}
                                </span>
                                <strong>{{ number_format((int) ($user_counter['active_user'] ?? 0)) }}</strong>
                            </div>
                            <div class="muqtarin-cust-pill">
                                <span class="muqtarin-cust-pill-top">
                                    <span class="muqtarin-cust-dot" style="background: #F6C667;"></span>
                                    {{ labels('admin_labels.inactive_customers', 'Inactive') }}
                                </span>
                                <strong>{{ number_format((int) ($user_counter['inactive_user'] ?? 0)) }}</strong>
                            </div>
                        </div>
                        @if ((int) ($user_counter['total_users'] ?? 0) === 0)
                            <div class="muqtarin-chart-zero-note mt-2">
                                <i class="bx bx-user-plus"></i>
                                <span>{{ labels('admin_labels.no_customers_yet', 'No customer activity recorded yet') }}</span>
                            </div>
                        @endif
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
                                        <p class="comm">{{ $item['orders_count'] }} {{ labels('admin_labels.orders', 'Orders') }}</p>
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
                    <div class="listOfContacts flex-grow-1" id="muqtarinDashboardContacts">
                        <div class="muqtarin-skeleton-list">
                            <div class="muqtarin-skeleton-item"></div>
                            <div class="muqtarin-skeleton-item"></div>
                            <div class="muqtarin-skeleton-item"></div>
                        </div>
                    </div>
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
                                data-side-pagination="server" data-pagination="true" data-page-size="5"
                                data-page-list="[5, 10, 20, 50]" data-search="true" data-show-columns="false"
                                data-show-refresh="true" data-trim-on-search="false" data-sort-name="id"
                                data-sort-order="desc" data-mobile-responsive="true" data-toolbar=""
                                data-show-export="false" data-maintain-selected="true"
                                data-icons-prefix="bx" data-icons='{"refresh":"bx-refresh"}'
                                data-query-params="queryParams">
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
                                <table class="table" id="seller_order_item_table" data-toggle="table"
                                    data-loading-template="loadingTemplate"
                                    data-url="{{ route('admin.orders.item_list') }}"
                                    data-click-to-select="true" data-side-pagination="server"
                                    data-pagination="true" data-page-size="5" data-page-list="[5, 10, 20, 50]"
                                    data-search="true" data-show-columns="false"
                                    data-show-refresh="true" data-trim-on-search="false"
                                    data-sort-name="id" data-sort-order="desc"
                                    data-mobile-responsive="true" data-toolbar=""
                                    data-show-export="false" data-maintain-selected="true"
                                    data-icons-prefix="bx" data-icons='{"refresh":"bx-refresh"}'
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th data-field="order_id" data-sortable="false">
                                                {{ labels('admin_labels.order_id', 'Order ID') }}
                                            </th>
                                            <th data-field="username" data-sortable="false">
                                                {{ labels('admin_labels.user_name', 'User Name') }}
                                            </th>
                                            <th data-field="payment_method" data-visible="true">
                                                {{ labels('admin_labels.payment_method', 'Payment Method') }}
                                            </th>
                                            <th data-field="active_status" data-sortable="false" data-visible="true">
                                                {{ labels('admin_labels.active_status', 'Active Status') }}
                                            </th>
                                            <th data-field="date_added">
                                                {{ labels('admin_labels.order_date', 'Order Date') }}
                                            </th>
                                            <th data-field="sub_total" data-sortable="false" data-visible="true">
                                                {{ labels('admin_labels.total', 'Total') }} ({{ $currency }})
                                            </th>
                                            <th data-field="operate" data-sortable="false">
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
        // Bootstrap-Table global formatters (defined before include_script auto-init)
        (function() {
            var lblNoMatches = {!! json_encode(labels('admin_labels.no_matching_records', 'No matching records found'), JSON_UNESCAPED_UNICODE) !!};
            var lblSearch = {!! json_encode(labels('admin_labels.search_records', 'Search records...'), JSON_UNESCAPED_UNICODE) !!};
            var lblLoading = {!! json_encode(labels('admin_labels.loading_data', 'Loading data...'), JSON_UNESCAPED_UNICODE) !!};
            var lblShowingRows = {!! json_encode(labels('admin_labels.table_showing_rows', 'Showing %s to %s of %s rows'), JSON_UNESCAPED_UNICODE) !!};
            var lblRowsPerPage = {!! json_encode(labels('admin_labels.table_rows_per_page', '%s rows per page'), JSON_UNESCAPED_UNICODE) !!};

            window.muqtarinTableNoMatches = function() {
                return '<div class="muqtarin-table-empty"><i class="bx bx-inbox"></i><span>' + lblNoMatches + '</span></div>';
            };
            window.muqtarinTableSearch = function() {
                return lblSearch;
            };
            window.muqtarinTableLoading = function() {
                return lblLoading;
            };
            window.muqtarinTableShowingRows = function(pageFrom, pageTo, totalRows) {
                return lblShowingRows.replace('%s', pageFrom).replace('%s', pageTo).replace('%s', totalRows);
            };
            window.muqtarinTableRowsPerPage = function(pageNumber) {
                return lblRowsPerPage.replace('%s', pageNumber);
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            // 1. 21st.dev Spotlight hover tracking on KPI cards
            document.querySelectorAll('.muqtarin-kpi-card').forEach(function(card) {
                card.addEventListener('mousemove', function(e) {
                    var rect = card.getBoundingClientRect();
                    card.style.setProperty('--mx', (e.clientX - rect.left) + 'px');
                    card.style.setProperty('--my', (e.clientY - rect.top) + 'px');
                });
            });

            // 2. Collapsible Store Setup Checklist (persisted in localStorage)
            var setupCard = document.getElementById('muqtarinSetupCard');
            var setupToggle = document.getElementById('muqtarinSetupToggle');
            if (setupCard && setupToggle) {
                var applySetupState = function(collapsed) {
                    setupCard.classList.toggle('is-collapsed', collapsed);
                    var icon = setupToggle.querySelector('i');
                    var textSpan = setupToggle.querySelector('span');
                    if (icon) icon.className = collapsed ? 'bx bx-chevron-down' : 'bx bx-chevron-up';
                    if (textSpan) {
                        textSpan.textContent = collapsed
                            ? setupToggle.getAttribute('data-expand-text')
                            : setupToggle.getAttribute('data-collapse-text');
                    }
                };
                try {
                    if (localStorage.getItem('muqtarin_setup_collapsed') === '1') {
                        applySetupState(true);
                    }
                } catch (e) {}
                setupToggle.addEventListener('click', function() {
                    var next = !setupCard.classList.contains('is-collapsed');
                    applySetupState(next);
                    try { localStorage.setItem('muqtarin_setup_collapsed', next ? '1' : '0'); } catch (e) {}
                });
            }

            // 3. Self-contained Chatify Recent Contacts Loader (no duplicate jQuery/CDNs)
            var contactsEl = document.getElementById('muqtarinDashboardContacts');
            if (contactsEl) {
                var contactsUrl = {!! json_encode($chatPrefixUrl . '/getContacts', JSON_UNESCAPED_UNICODE) !!};
                var chatIndexUrl = {!! json_encode(route('admin.chat.index'), JSON_UNESCAPED_UNICODE) !!};
                var lblNoMessages = {!! json_encode(labels('admin_labels.no_messages_yet', 'No recent conversations yet'), JSON_UNESCAPED_UNICODE) !!};
                var lblOpenChat = {!! json_encode(labels('admin_labels.open_messenger', 'Open Chat'), JSON_UNESCAPED_UNICODE) !!};

                fetch(contactsUrl + '?page=1', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                .then(function(res) { return res.ok ? res.json() : Promise.reject(res.status); })
                .then(function(data) {
                    if (!data || Number(data.total || 0) === 0 || !data.contacts || data.contacts.indexOf('messenger-list-item') === -1) {
                        contactsEl.innerHTML =
                            '<div class="muqtarin-empty-state">' +
                                '<i class="bx bx-message-square-dots"></i>' +
                                '<p>' + lblNoMessages + '</p>' +
                                '<a href="' + chatIndexUrl + '" class="btn btn-sm btn-outline-primary mt-1">' +
                                    '<i class="bx bx-chat"></i><span>' + lblOpenChat + '</span>' +
                                '</a>' +
                            '</div>';
                        return;
                    }
                    contactsEl.innerHTML = data.contacts;
                    contactsEl.querySelectorAll('.messenger-list-item').forEach(function(item) {
                        item.addEventListener('click', function() {
                            window.location.href = chatIndexUrl;
                        });
                    });
                })
                .catch(function() {
                    contactsEl.innerHTML =
                        '<div class="muqtarin-empty-state">' +
                            '<i class="bx bx-message-square-dots"></i>' +
                            '<p>' + lblNoMessages + '</p>' +
                            '<a href="' + chatIndexUrl + '" class="btn btn-sm btn-outline-primary mt-1">' +
                                '<i class="bx bx-chat"></i><span>' + lblOpenChat + '</span>' +
                            '</a>' +
                        '</div>';
                });
            }

            // 4. Analytics Charts (ApexCharts) + Period Summary Pills
            if (typeof ApexCharts === 'undefined') return;

            var isDarkTheme = function() {
                return document.documentElement.getAttribute('data-theme') === 'dark';
            };
            var isRtl = document.documentElement.getAttribute('dir') === 'rtl';

            var labelSales = {!! json_encode(labels('admin_labels.sales', 'Sales'), JSON_UNESCAPED_UNICODE) !!};
            var labelRevenue = {!! json_encode(labels('admin_labels.revenue', 'Revenue'), JSON_UNESCAPED_UNICODE) !!};
            var labelCommission = {!! json_encode(labels('admin_labels.commission', 'Commission'), JSON_UNESCAPED_UNICODE) !!};
            var labelNew = {!! json_encode(labels('admin_labels.new', 'New'), JSON_UNESCAPED_UNICODE) !!};
            var labelActive = {!! json_encode(labels('admin_labels.active', 'Active'), JSON_UNESCAPED_UNICODE) !!};
            var labelDeactive = {!! json_encode(labels('admin_labels.deactive', 'Inactive'), JSON_UNESCAPED_UNICODE) !!};
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

            var sumArray = function(arr) {
                return (arr || []).reduce(function(acc, val) { return acc + Number(val || 0); }, 0);
            };
            var formatShortNumber = function(num) {
                return Number(num || 0).toLocaleString();
            };
            var updatePeriodSummary = function(periodData) {
                if (!periodData || !periodData.series) return;
                var totalSales = sumArray(periodData.series[0].data);
                var totalRev = sumArray(periodData.series[1].data);
                var totalComm = sumArray(periodData.series[2].data);

                var elSales = document.getElementById('muqtarinKpiSales');
                var elRev = document.getElementById('muqtarinKpiRevenue');
                var elComm = document.getElementById('muqtarinKpiCommission');
                var zeroNote = document.getElementById('muqtarinRevenueZeroNote');

                if (elSales) elSales.textContent = formatShortNumber(totalSales);
                if (elRev) elRev.textContent = currencySymbol + ' ' + formatShortNumber(totalRev);
                if (elComm) elComm.textContent = currencySymbol + ' ' + formatShortNumber(totalComm);
                if (zeroNote) {
                    zeroNote.classList.toggle('d-none', (totalSales + totalRev + totalComm) > 0);
                }
            };

            var getYAxisConfig = function(periodData) {
                var allVals = [];
                if (periodData && periodData.series) {
                    periodData.series.forEach(function(s) {
                        (s.data || []).forEach(function(v) {
                            var n = Number(v);
                            if (Number.isFinite(n)) allVals.push(n);
                        });
                    });
                }
                var maxVal = allVals.length ? Math.max.apply(null, allVals) : 0;
                var hasNonZero = maxVal > 0;
                return {
                    min: 0,
                    max: hasNonZero ? undefined : 10,
                    tickAmount: hasNonZero ? undefined : 5,
                    forceNiceScale: hasNonZero,
                    labels: {
                        formatter: function(value) {
                            var v = Number(value);
                            if (!Number.isFinite(v) || v < 0) return '0';
                            if (v >= 1000) return (v / 1000).toFixed(1) + 'k';
                            return Math.round(v).toString();
                        }
                    }
                };
            };

            let chartData = data['Daily'];
            updatePeriodSummary(chartData);
            var dark = isDarkTheme();

            var adminOptions = {
                series: chartData.series,
                colors: ['#22C55E', '#3B82F6', '#F6C667'],
                chart: {
                    height: 300,
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
                    tickAmount: 8,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        rotate: 0,
                        hideOverlappingLabels: true,
                        trim: true,
                        style: { fontSize: '11px' }
                    }
                },
                yaxis: getYAxisConfig(chartData),
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
                        formatter: function(val, opts) {
                            if (opts && opts.seriesIndex === 0) {
                                return formatShortNumber(val);
                            }
                            return currencySymbol + ' ' + formatShortNumber(val);
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: isRtl ? 'right' : 'left'
                }
            };

            var adminChartEl = document.querySelector(".admin_statistic_chart");
            var adminChart = adminChartEl ? new ApexCharts(adminChartEl, adminOptions) : null;
            if (adminChart) {
                adminChart.render();
            }

            document.querySelectorAll(".overview-statistic .chart-action li a").forEach(function(tab) {
                tab.addEventListener("click", function(e) {
                    e.preventDefault();
                    document.querySelectorAll(".chart-action li a").forEach(function(a) {
                        a.classList.remove('active');
                    });
                    tab.classList.add('active');

                    var key = tab.getAttribute("href").replace('#', '');
                    chartData = data[key];
                    updatePeriodSummary(chartData);
                    if (adminChart && chartData) {
                        adminChart.updateOptions({
                            series: chartData.series,
                            xaxis: { categories: chartData.categories },
                            yaxis: getYAxisConfig(chartData)
                        });
                    }
                });
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
                    height: 235,
                    type: 'donut',
                    fontFamily: "'Tajawal', 'Inter', sans-serif",
                    foreColor: dark ? '#94A3B8' : '#64748B'
                },
                stroke: {
                    colors: [dark ? '#161B22' : '#FFFFFF'],
                    width: 2
                },
                legend: { show: false },
                colors: ['#2563EB', '#22C55E', '#F6C667'],
                dataLabels: { enabled: false },
                tooltip: {
                    theme: dark ? 'dark' : 'light'
                },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
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
        });
    </script>
@endsection

