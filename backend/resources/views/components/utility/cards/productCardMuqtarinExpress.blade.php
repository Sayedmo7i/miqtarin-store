@props(['details'])
@php
    use App\Models\Brand;
    use App\Services\TranslationService;
    use App\Services\MediaService;
    use App\Services\CurrencyService;
    $language_code = app(TranslationService::class)->getLanguageCode();
    $is_combo = ($details->type ?? '') === 'combo-product';
    $product_url = $is_combo ? customUrl('combo-products/' . $details->slug) : customUrl('products/' . $details->slug);
    $main_img = app(MediaService::class)->dynamic_image($details->image, 520);
    $brand_name = !$is_combo && !empty($details->brand)
        ? app(TranslationService::class)->getDynamicTranslation(Brand::class, 'name', $details->brand, $language_code)
        : '';
@endphp
<div class="item col-item h-100">
    <div class="mq-card mq-card--express {{ isset($details->store) && $details->store->status != 1 ? 'store-inactive' : '' }}">
        @if (isset($details->store) && $details->store->status != 1)
            <div class="product-not-available">
                <h2>{{ labels('front_messages.product_not_available', 'Product Not Available') }}</h2>
            </div>
        @endif
        <div class="mq-card-media product-image">
            <a wire:navigate href="{{ $product_url }}" class="mq-card-img-link slider-link" data-link="{{ $product_url }}">
                <img class="primary blur-up lazyload" data-src="{{ $main_img }}" src="{{ $main_img }}"
                    alt="{{ $details->name }}" title="{{ $details->name }}" />
            </a>

            <div class="mq-card-badges">
                @if ($is_combo)
                    <span class="mq-badge mq-badge-combo">{{ labels('front_messages.combo_products', 'Combo') }}</span>
                @elseif (!empty($details->min_max_price['discount_in_percentage']) && $details->min_max_price['discount_in_percentage'] > 0)
                    <span class="mq-badge mq-badge-gold">-{{ $details->min_max_price['discount_in_percentage'] }}%</span>
                @elseif (!empty($details->new_arrival))
                    <span class="mq-badge mq-badge-pine">{{ labels('front_messages.new_arrivals', 'New') }}</span>
                @endif
            </div>

            <div class="mq-card-tools button-set">
                <a href="#quickview-modal" class="btn-icon quickview quick-view-modal" data-bs-toggle="modal"
                    data-bs-target="#quickview_modal" data-product-id="{{ $details->id }}"
                    @if ($is_combo) data-product-type="combo-product" @endif
                    title="{{ labels('front_messages.quick_view', 'Quick View') }}">
                    <i class="hdr-icon icon anm anm-search-l"></i>
                    <span class="text">{{ labels('front_messages.quick_view', 'Quick View') }}</span>
                </a>
                @livewire('products.wishlist-button', ['product_id' => $details->id], key('wishlist-mq-ex-' . $details->id . '-' . ($is_combo ? 'c' : 'r')))
            </div>
        </div>

        <div class="mq-card-body">
            <div class="mq-card-meta-top">
                @if (!empty($brand_name) && !empty($details->brand_slug))
                    <a wire:navigate href="{{ customUrl('products/?brand=' . $details->brand_slug) }}"
                        class="mq-card-vendor slider-link"
                        data-link="{{ customUrl('products/?brand=' . $details->brand_slug) }}">{!! $brand_name !!}</a>
                @else
                    <span class="mq-card-vendor">{{ labels('front_messages.express_pick', 'Express Pick') }}</span>
                @endif
                @if (!empty($details->rating) && $details->rating > 0)
                    <span class="mq-rating-pill"><i class="bx bxs-star"></i> {{ number_format((float) $details->rating, 1) }}</span>
                @endif
            </div>

            <h3 class="mq-card-title">
                <a wire:navigate href="{{ $product_url }}" class="slider-link"
                    data-link="{{ $product_url }}" title="{!! $details->name !!}">{!! $details->name !!}</a>
            </h3>

            @php
                if ($is_combo) {
                    $price = app(CurrencyService::class)->currentCurrencyPrice($details->price, true);
                    $special_price = !empty($details->special_price) && $details->special_price > 0
                        ? app(CurrencyService::class)->currentCurrencyPrice($details->special_price, true)
                        : $price;
                } else {
                    $price = app(CurrencyService::class)->currentCurrencyPrice($details->min_max_price['max_price'], true);
                    $special_price = !empty($details->min_max_price['special_min_price']) && $details->min_max_price['special_min_price'] > 0
                        ? app(CurrencyService::class)->currentCurrencyPrice($details->min_max_price['special_min_price'], true)
                        : $price;
                }
            @endphp
            <div class="mq-card-price-row product-price">
                <span class="mq-price-now price fw-bold">{{ $special_price }}</span>
                @if ($special_price !== $price)
                    <span class="mq-price-old price old-price">{{ $price }}</span>
                @endif
            </div>

            <div class="pt-1">
                @if (!$is_combo && ($details->type ?? '') === 'variable_product')
                    <a href="#quickview-modal"
                        class="mq-card-cta add_cart quickview quick-view-modal"
                        data-bs-toggle="modal" data-bs-target="#quickview_modal"
                        data-product-id="{{ $details->id }}" data-product-variant-id="">
                        <i class="bx bx-plus-circle"></i>
                        <span class="text">{{ labels('front_messages.add', 'Add') }}</span>
                    </a>
                @elseif (!$is_combo)
                    <div class="addtocart-btn add_cart"
                        data-product-variant-id="{{ $details->variants[0]['id'] }}"
                        data-name="{{ $details->name }}" data-slug="{{ $details->slug }}"
                        data-brand-name="{!! $brand_name !!}" data-image="{{ $main_img }}"
                        data-product-type="regular" data-max="{{ $details->total_allowed_quantity }}"
                        data-step="{{ $details->quantity_step_size }}"
                        data-min="{{ $details->minimum_order_quantity }}"
                        data-stock-type="{{ $details->stock_type }}" data-store-id="{{ $details->store_id }}"
                        data-variant-price="{{ app(CurrencyService::class)->currentCurrencyPrice($details->variants[0]['special_price']) }}">
                        <a class="mq-card-cta cursor-pointer" data-product-id="{{ $details->id }}">
                            <i class="bx bx-plus-circle"></i>
                            <span class="text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                        </a>
                    </div>
                @else
                    <div class="addtocart-btn add_cart"
                        data-product-variant-id="{{ $details->id }}"
                        data-name="{{ $details->name }}" data-slug="{{ $details->slug }}" data-brand-name=""
                        data-image="{{ $main_img }}" data-product-type="combo"
                        data-max="{{ $details->total_allowed_quantity }}"
                        data-step="{{ $details->quantity_step_size }}"
                        data-min="{{ $details->minimum_order_quantity }}"
                        data-stock-type="{{ $details->stock_type }}" data-store-id="{{ $details->store_id }}"
                        data-variant-price="{{ app(CurrencyService::class)->currentCurrencyPrice($details->special_price) }}">
                        <a class="mq-card-cta cursor-pointer" data-product-id="{{ $details->id }}">
                            <i class="bx bx-plus-circle"></i>
                            <span class="text">{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
