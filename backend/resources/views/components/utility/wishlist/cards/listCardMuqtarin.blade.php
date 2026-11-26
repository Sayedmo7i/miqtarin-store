@props([
    'regular_wishlist' => [],
    'combo_wishlist' => [],
    'favorites_count' => 0,
    'links' => null,
])
@php
    use App\Models\Product;
    use App\Models\ComboProduct;
    use App\Services\TranslationService;
    use App\Services\MediaService;
    use App\Services\CurrencyService;
    $language_code = app(TranslationService::class)->getLanguageCode();
@endphp
<div class="col-12 col-sm-12 col-md-12 col-lg-9">
    <div class="dashboard-content h-100">
        <div class="h-100" id="wishlist">
            <div class="mq-wishlist-card mt-0 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div>
                        <span class="mq-eyebrow mq-eyebrow-light mb-2">{{ labels('front_messages.saved_items', 'Saved Items') }}</span>
                        <h2 class="mb-0" style="color: var(--mq-ink);">{{ labels('front_messages.my_wishlist', 'My Wishlist') }}</h2>
                    </div>
                </div>

                <div class="tabs-listing section p-0">
                    <ul class="product-tabs style2 list-unstyled d-flex-wrap d-none d-md-flex" wire:ignore>
                        <li rel="regular-wishlist" class="active"><a
                                class="tablink">{{ labels('front_messages.regular_products', 'Regular Products') }}</a>
                        </li>
                        <li rel="combo-wishlist"><a
                                class="tablink">{{ labels('front_messages.combo_products', 'Combo Products') }}</a>
                        </li>
                    </ul>

                    <div class="tab-container">
                        <h3 class="tabs-ac-style rounded-5 d-md-none active" rel="regular-wishlist">
                            {{ labels('front_messages.regular_products', 'Regular Products') }}</h3>
                        <div id="regular-wishlist" class="tab-content" wire:ignore.self>
                            @if (count($regular_wishlist) >= 1)
                                <div class="mq-wishlist-grid">
                                    @foreach ($regular_wishlist as $details)
                                        @php
                                            $pro_image = app(MediaService::class)->dynamic_image($details['image'], 600);
                                            $pro_name = app(TranslationService::class)->getDynamicTranslation(Product::class, 'name', $details['id'], $language_code);
                                            if (($details['type'] ?? '') === 'variable_product') {
                                                $price = app(CurrencyService::class)->currentCurrencyPrice($details['min_max_price']['max_price'], true);
                                                $special_price = !empty($details['min_max_price']['special_min_price']) && $details['min_max_price']['special_min_price'] > 0
                                                    ? app(CurrencyService::class)->currentCurrencyPrice($details['min_max_price']['special_min_price'], true)
                                                    : $price;
                                            } else {
                                                $price = app(CurrencyService::class)->currentCurrencyPrice($details['variants'][0]['price'], true);
                                                $special_price = !empty($details['variants'][0]['special_price']) && $details['variants'][0]['special_price'] > 0
                                                    ? app(CurrencyService::class)->currentCurrencyPrice($details['variants'][0]['special_price'], true)
                                                    : $price;
                                            }
                                        @endphp
                                        <div class="mq-wish-item">
                                            <button type="button" wire:click="removeFavorite({{ $details['id'] }})"
                                                class="mq-wish-remove remove-favorite" data-product-id="{{ $details['id'] }}"
                                                title="{{ labels('front_messages.remove_from_wishlist', 'Remove') }}">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </button>
                                            <a wire:navigate href="{{ customUrl('products/' . $details['slug']) }}"
                                                class="mq-card-media rounded-3 d-block">
                                                <img class="blur-up lazyload" src="{{ $pro_image }}" alt="{{ $pro_name }}" />
                                            </a>
                                            <div class="d-flex flex-column gap-1 flex-grow-1">
                                                <h4 class="mq-card-title">
                                                    <a wire:navigate href="{{ customUrl('products/' . $details['slug']) }}">{{ $pro_name }}</a>
                                                </h4>
                                                <div class="mq-card-price-row">
                                                    <span class="mq-price-now price fw-bold">{{ $special_price }}</span>
                                                    @if ($special_price !== $price)
                                                        <span class="mq-price-old price old-price">{{ $price }}</span>
                                                    @endif
                                                </div>
                                                @if (!empty($details['rating']))
                                                    <span class="mq-rating-pill align-self-start"><i class="bx bxs-star"></i> {{ $details['rating'] }}</span>
                                                @endif
                                            </div>
                                            <div class="pt-1">
                                                @if (($details['type'] ?? '') == 'variable_product')
                                                    <a href="#quickview-modal" data-bs-toggle="modal" data-bs-target="#quickview_modal"
                                                        class="mq-card-cta add_cart quickview quick-view-modal"
                                                        data-product-id="{{ $details['id'] }}" data-product-variant-id="">
                                                        <i class="anm anm-bag-l"></i>
                                                        <span>{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                                    </a>
                                                @else
                                                    <a class="mq-card-cta add_cart cursor-pointer"
                                                        data-product-id="{{ $details['id'] }}"
                                                        data-product-variant-id="{{ $details['variants'][0]['id'] }}"
                                                        data-name="{{ $pro_name }}" data-slug="{{ $details['slug'] }}"
                                                        data-brand-name="{{ $details['brand'] }}" data-image="{{ $details['image'] }}"
                                                        data-product-type="regular"
                                                        data-max="{{ $details['total_allowed_quantity'] }}"
                                                        data-step="{{ $details['quantity_step_size'] }}"
                                                        data-min="{{ $details['minimum_order_quantity'] }}"
                                                        data-store-id="{{ $details['store_id'] }}"
                                                        data-stock-type="{{ $details['stock_type'] }}">
                                                        <i class="anm anm-bag-l"></i>
                                                        <span>{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="opacity-50"><ion-icon name="bookmark-outline" class="address-location-icon text-muted"></ion-icon></div>
                                    <div class="fs-6 fw-500">{{ labels('front_messages.wishlist_is_empty', 'Your Wishlist Appears to be Empty.') }}</div>
                                </div>
                            @endif
                        </div>

                        <h3 class="tabs-ac-style d-md-none" rel="combo-wishlist">
                            {{ labels('front_messages.combo_products', 'Combo Products') }}</h3>
                        <div id="combo-wishlist" class="tab-content" wire:ignore.self>
                            @if (count($combo_wishlist) >= 1)
                                <div class="mq-wishlist-grid">
                                    @foreach ($combo_wishlist as $details)
                                        @php
                                            $pro_image = app(MediaService::class)->dynamic_image($details['image'], 600);
                                            $combo_pro_name = app(TranslationService::class)->getDynamicTranslation(ComboProduct::class, 'title', $details['id'], $language_code);
                                            $price = app(CurrencyService::class)->currentCurrencyPrice($details['price'], true);
                                            $special_price = !empty($details['special_price']) && $details['special_price'] > 0
                                                ? app(CurrencyService::class)->currentCurrencyPrice($details['special_price'], true)
                                                : $price;
                                        @endphp
                                        <div class="mq-wish-item">
                                            <button type="button" wire:click="removeFavorite({{ $details['id'] }})"
                                                class="mq-wish-remove remove-favorite" data-product-id="{{ $details['id'] }}" data-product-type="combo"
                                                title="{{ labels('front_messages.remove_from_wishlist', 'Remove') }}">
                                                <ion-icon name="close-outline"></ion-icon>
                                            </button>
                                            <a wire:navigate href="{{ customUrl('combo-products/' . $details['slug']) }}"
                                                class="mq-card-media rounded-3 d-block">
                                                <img class="blur-up lazyload" src="{{ $pro_image }}" alt="{{ $combo_pro_name }}" />
                                            </a>
                                            <div class="d-flex flex-column gap-1 flex-grow-1">
                                                <h4 class="mq-card-title">
                                                    <a wire:navigate href="{{ customUrl('combo-products/' . $details['slug']) }}">{{ $combo_pro_name }}</a>
                                                </h4>
                                                <div class="mq-card-price-row">
                                                    <span class="mq-price-now price fw-bold">{{ $special_price }}</span>
                                                    @if ($special_price !== $price)
                                                        <span class="mq-price-old price old-price">{{ $price }}</span>
                                                    @endif
                                                </div>
                                                @if (!empty($details['rating']))
                                                    <span class="mq-rating-pill align-self-start"><i class="bx bxs-star"></i> {{ $details['rating'] }}</span>
                                                @endif
                                            </div>
                                            <div class="pt-1">
                                                <a class="mq-card-cta add_cart cursor-pointer"
                                                    data-product-id="{{ $details['id'] }}"
                                                    data-product-variant-id="{{ $details['id'] }}"
                                                    data-name="{{ $combo_pro_name }}" data-slug="{{ $details['slug'] }}" data-brand-name=""
                                                    data-image="{{ $details['image'] }}" data-product-type="combo"
                                                    data-max="{{ $details['total_allowed_quantity'] }}"
                                                    data-step="{{ $details['quantity_step_size'] }}"
                                                    data-min="{{ $details['minimum_order_quantity'] }}" data-stock-type="">
                                                    <i class="anm anm-bag-l"></i>
                                                    <span>{{ labels('front_messages.add_to_cart', 'Add to Cart') }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="d-flex flex-column justify-content-center align-items-center py-5">
                                    <div class="opacity-50"><ion-icon name="bookmark-outline" class="address-location-icon text-muted"></ion-icon></div>
                                    <div class="fs-6 fw-500">{{ labels('front_messages.wishlist_is_empty', 'Your Wishlist Appears to be Empty.') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>{!! $links !!}</div>
</div>
