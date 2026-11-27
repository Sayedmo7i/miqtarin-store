@php
    use App\Models\Category;
    use App\Models\Brand;
    use App\Services\TranslationService;
    use App\Services\StoreService;
    use App\Services\MediaService;
    use App\Services\CurrencyService;
    $category = fetchDetails(\App\Models\Category::class, ['id' => $product_details->category_id], 'slug');
    $category_slug = $category[0]->slug ?? '';
    $category_name = app(TranslationService::class)->getDynamicTranslation(Category::class, 'name', $product_details->category_id, $language_code);
    $brand_name = !empty($product_details->brand)
        ? app(TranslationService::class)->getDynamicTranslation(Brand::class, 'name', $product_details->brand, $language_code)
        : '';
    $main_image = app(MediaService::class)->dynamic_image($product_details->image, 650);
    $main_image_zoom = app(MediaService::class)->dynamic_image($product_details->image, 900);
    $discount = $product_details->min_max_price['discount_in_percentage'] ?? null;
    if (count($product_details->variants) <= 1) {
        $variant_id = $product_details->variants[0]['id'];
        $variant_price = $product_details->variants[0]['special_price'];
    } else {
        $variant_id = '';
        $variant_price = '';
    }
@endphp
<div id="page-content" wire:ignore class="mq-page mq-details-shell">
    <div class="mq-container">
        <div class="mq-details-breadcrumb">
            <a wire:navigate href="{{ customUrl('/') }}" title="{{ labels('front_messages.back_to_home_page', 'Back to the home page') }}">
                {{ labels('front_messages.home', 'Home') }}
            </a>
            <ion-icon class="icon" name="chevron-forward-outline"></ion-icon>
            <span class="fw-semibold">{!! $bread_crumb['page_main_bread_crumb'] ?? '' !!}</span>
            @if (isset($bread_crumb['right_breadcrumb']) && !empty($bread_crumb['right_breadcrumb']))
                @foreach ($bread_crumb['right_breadcrumb'] as $right_breadcrumb)
                    <ion-icon class="icon" name="chevron-forward-outline"></ion-icon>
                    <span class="fw-semibold">{!! request()->is('products/*') ? strip_tags($right_breadcrumb) : $right_breadcrumb !!}</span>
                @endforeach
            @endif
        </div>

        <div class="product-single {{ isset($product_details->store) && $product_details->store->status != 1 ? 'store-inactive' : '' }}">
            @if (isset($product_details->store) && $product_details->store->status != 1)
                <div class="product-not-available">
                    <h2>{{ labels('front_messages.product_not_available', 'Product Not Available') }}</h2>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-9 col-md-12 product-layout-img-info">
                    <div class="row g-4">
                        <!-- Gallery Card -->
                        <div class="col-lg-6 col-md-6 col-12 product-layout-img">
                            <div class="mq-details-gallery-card">
                                <div class="product-details-img product-horizontal-style">
                                    <div class="zoompro-wrap rounded-4 overflow-hidden">
                                        <div class="zoompro-span">
                                            <img id="zoompro" class="zoompro w-100 rounded-4"
                                                src="{{ $main_image }}" data-zoom-image="{{ $main_image_zoom }}"
                                                alt="{{ $product_details->name }}" width="625" height="720">
                                        </div>
                                    </div>
                                    <div class="product-thumb product-horizontal-thumb mt-3">
                                        <div id="gallery" class="product-thumb-horizontal">
                                            @foreach ($product_details->other_images as $images)
                                                @php
                                                    $other_image = app(MediaService::class)->dynamic_image($images, 600);
                                                    $other_image_zoom = app(MediaService::class)->dynamic_image($images, 800);
                                                @endphp
                                                <div class="slick-slide">
                                                    <a data-image="{{ $other_image }}" data-zoom-image="{{ $other_image_zoom }}">
                                                        <img class="blur-up lazyloaded rounded-3" src="{{ $other_image }}"
                                                            alt="{{ $product_details->name }}" width="625" height="720">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="social-sharing d-flex-center justify-content-center mt-3 pt-2 border-top lh-lg">
                                    <span class="sharing-lbl fw-600 me-2">{{ labels('front_messages.share', 'Share') }}:</span>
                                    <div class="shareon">
                                        <a class="facebook" data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                        <a class="telegram" data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                        <a class="twitter" data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                        <a class="whatsapp" data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                        <a class="email" data-text="Take a Look at this {{ $product_details->name }} on {{ $system_settings['app_name'] }}"></a>
                                        <a class="copy-url"></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buy Card -->
                        <div class="col-lg-6 col-md-6 col-12 product-layout-info">
                            <div class="mq-details-buy-card">
                                <div class="product-single-meta">
                                    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
                                        @if ($category_slug)
                                            <a wire:navigate href="{{ customUrl('categories/' . $category_slug . '/products') }}"
                                                class="mq-eyebrow mq-eyebrow-light text-decoration-none">
                                                <ion-icon name="layers-outline"></ion-icon>
                                                <span>{!! $category_name !!}</span>
                                            </a>
                                        @endif
                                        <div class="product-review d-flex-center">
                                            <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                                                class="kv-ltr-theme-svg-star rating-loading d-none"
                                                value="{{ $product_details->rating }}" dir="ltr" data-size="xs"
                                                data-show-clear="false" data-show-caption="false" readonly>
                                        </div>
                                    </div>

                                    <h1 class="product-main-title h3 fw-bold mb-2" style="color: var(--mq-ink);">
                                        {{ $product_details->name }}
                                    </h1>
                                    <p class="small mb-3" style="color: var(--mq-muted); line-height: 1.6;">
                                        {{ $product_details->short_description }}
                                    </p>

                                    @if (count($product_details->tags) >= 1)
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            @foreach ($product_details->tags as $tag)
                                                <a href="{{ customUrl('products/?tag=' . $tag) }}"
                                                    class="mq-badge mq-badge-pine text-decoration-none tag-filter"
                                                    title="{!! $tag !!}">#{!! $tag !!}</a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="d-flex flex-wrap gap-3 small mb-3" style="color: var(--mq-muted);">
                                        @if (!empty($product_details->sku))
                                            <span><strong>{{ labels('front_messages.sku', 'SKU') }}:</strong> {{ $product_details->sku }}</span>
                                        @endif
                                        @if (!empty($product_details->made_in))
                                            <span><strong>{{ labels('front_messages.made_in', 'Made In') }}:</strong> {{ $product_details->made_in }}</span>
                                        @endif
                                    </div>

                                    <!-- Price Block -->
                                    <div class="product-price d-flex align-items-baseline gap-2 flex-wrap my-3 p-3 rounded-4"
                                        style="background: var(--mq-surface-alt); border: 1px solid var(--mq-border);">
                                        @if ($product_details->type != 'variable_product')
                                            @php
                                                $price = app(CurrencyService::class)->currentCurrencyPrice($product_details->variants[0]['price'], true);
                                                $special_price = $product_details->variants[0]['special_price'] && $product_details->variants[0]['special_price'] > 0
                                                    ? app(CurrencyService::class)->currentCurrencyPrice($product_details->variants[0]['special_price'], true)
                                                    : $price;
                                            @endphp
                                            <span class="mq-price-now fs-4 product_price" id="price">{{ $special_price }}</span>
                                            <span class="mq-price-old old-price">{{ $special_price !== $price ? $price : '' }}</span>
                                        @else
                                            @php
                                                $max_price = app(CurrencyService::class)->currentCurrencyPrice($product_details->min_max_price['max_price'], true);
                                                $special_min_price = $product_details->min_max_price['special_min_price'] && $product_details->min_max_price['special_min_price'] > 0
                                                    ? app(CurrencyService::class)->currentCurrencyPrice($product_details->min_max_price['special_min_price'], true)
                                                    : $max_price;
                                            @endphp
                                            <span class="mq-price-now fs-4 product_price" id="price">{{ $max_price }} - {{ $special_min_price }}</span>
                                        @endif
                                        @if ($discount)
                                            <span class="mq-badge mq-badge-gold discount-sup">-{{ $discount }}%</span>
                                        @endif
                                    </div>

                                    <!-- Stock Status -->
                                    @if ($product_details->product_type == 'simple_product')
                                        @if ($product_details->stock_type != '')
                                            <p class="product-stock d-flex align-items-center gap-2 mb-3">
                                                <span>{{ labels('front_messages.availability', 'Availability') }}:</span>
                                                <span class="pro-stockLbl ps-0">
                                                    @if ($product_details->availability >= 1)
                                                        <span class="mq-badge mq-badge-combo stockLbl instock text-uppercase">{{ labels('front_messages.in_stock', 'In stock') }}</span>
                                                    @else
                                                        <span class="badge bg-danger stockLbl outstock text-uppercase">{{ labels('front_messages.out_of_stock', 'Out of Stock') }}</span>
                                                    @endif
                                                </span>
                                            </p>
                                        @endif
                                    @else
                                        @if ($product_details->variants[0]['stock_type'] != '')
                                            <p class="product-stock d-flex align-items-center gap-2 mb-3">
                                                <span>{{ labels('front_messages.availability', 'Availability') }}:</span>
                                                <span class="pro-stockLbl ps-0">
                                                    @if ($product_details->variants[0]['availability'] >= 1)
                                                        <span class="mq-badge mq-badge-combo stockLbl instock text-uppercase">{{ labels('front_messages.in_stock', 'In stock') }}</span>
                                                    @else
                                                        <span class="badge bg-danger stockLbl outstock text-uppercase">{{ labels('front_messages.out_of_stock', 'Out of Stock') }}</span>
                                                    @endif
                                                </span>
                                            </p>
                                        @endif
                                    @endif
                                </div>

                                <!-- Swatches -->
                                <div class="product-swatches-option">
                                    @foreach ($product_details->attributes as $attributes)
                                        @php
                                            $attribute_ids = explode(',', $attributes['ids']);
                                            $attribute_values = explode(',', $attributes['value']);
                                            $variantsCollection = collect($product_details->variants);
                                            $uniqueAttributes = array_unique($attribute_values);
                                        @endphp
                                        <div class="product-item swatches-size w-100 mb-3 swatch-1 option2" data-option-index="1">
                                            <label class="label d-flex align-items-center fw-semibold mb-1">{{ $attributes['name'] }}</label>
                                            <ul class="variants-size size-swatches d-flex-center pt-1 clearfix flex-wrap gap-2">
                                                @foreach ($uniqueAttributes as $key => $val)
                                                    @php
                                                        $variant = $variantsCollection->firstWhere('attribute_value_ids', (string) ($attribute_ids[$key] ?? ''));
                                                        $variantImage = !empty($variant->images_md[0]) ? $variant->images_md[0] : $product_details->image;
                                                    @endphp
                                                    <li class="swatch x-large available p-1 toggleInput" onclick="toggleInput(this)">
                                                        <input type="radio" class="swatchLbl attributes d-none"
                                                            data-bs-toggle="tooltip" value="{{ $attribute_ids[$key] ?? '' }}"
                                                            data-bs-placement="top" title="{{ $val }}"
                                                            data-image="{{ $variantImage }}" data-zoom-image="{{ $variantImage }}"
                                                            id="variant-{{ $attribute_ids[$key] ?? $key }}">
                                                        {{ $val }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                                @foreach ($product_details->variants as $variant)
                                    <input type="hidden" class="variants" name="variants_ids" data-image-index="" data-name=""
                                        value="{{ $variant['attribute_value_ids'] }}" data-id="{{ $variant['id'] }}"
                                        data-price="{{ app(CurrencyService::class)->currentCurrencyPrice($variant['price']) }}"
                                        data-special_price="{{ app(CurrencyService::class)->currentCurrencyPrice($variant['special_price']) }}" />
                                @endforeach

                                <!-- Actions -->
                                <div class="product-action w-100 d-flex flex-wrap gap-2 my-3">
                                    <div class="product-form-quantity d-flex-center">
                                        <div class="qtyField">
                                            <button class="qtyBtn minus" href="#;"><ion-icon name="remove-outline"></ion-icon></button>
                                            <input type="number" name="quantity" value="1" class="product-form-input qty dlt-qty"
                                                max='{{ $product_details->total_allowed_quantity == 0 ? 'Infinity' : $product_details->total_allowed_quantity }}'
                                                step='{{ $product_details->quantity_step_size }}'
                                                min='{{ $product_details->minimum_order_quantity }}' />
                                            <button class="qtyBtn plus" href="#;"><ion-icon name="add-outline"></ion-icon></button>
                                        </div>
                                    </div>

                                    <div class="product-form-submit addcart flex-grow-1">
                                        <button type="submit" name="add"
                                            class="mq-btn-primary w-100 product-form-cart-submit add_cart dlt-add-cart"
                                            data-product-reference-id="{{ isset($reference_id) && !empty($reference_id) ? $reference_id : '' }}"
                                            data-product-variant-id="{{ $variant_id }}"
                                            data-max='{{ $product_details->total_allowed_quantity }}'
                                            data-step='{{ $product_details->quantity_step_size }}'
                                            data-min='{{ $product_details->minimum_order_quantity }}'
                                            data-store-id='{{ $product_details->store_id }}'
                                            data-variant-price="{{ $variant_price }}" data-product-type='regular'>
                                            <i class="anm anm-bag-l"></i>
                                            <span>{{ labels('front_messages.add_to_cart', 'Add to cart') }}</span>
                                        </button>
                                    </div>

                                    <div class="product-form-submit buyit d-flex w-100 gap-2 mt-1">
                                        <button type="submit" class="mq-btn-gold flex-grow-1 buy_now add_cart dlt-add-cart"
                                            data-product-reference-id="{{ isset($reference_id) && !empty($reference_id) ? $reference_id : '' }}"
                                            data-product-variant-id="{{ $variant_id }}"
                                            data-name='{{ $product_details->name }}'
                                            data-slug='{{ $product_details->slug }}'
                                            data-brand_name='{{ $brand_name }}'
                                            data-image='{{ $product_details->image }}' data-product-type='regular'
                                            data-max='{{ $product_details->total_allowed_quantity }}'
                                            data-step='{{ $product_details->quantity_step_size }}'
                                            data-min='{{ $product_details->minimum_order_quantity }}'
                                            data-store-id='{{ $product_details->store_id }}'
                                            data-variant-price="{{ $variant_price }}">
                                            <span>{{ labels('front_messages.buy_it_now', 'Buy it now') }}</span>
                                        </button>
                                        <a href="#"
                                            class="mq-icon-btn wishlist-submit remove-favorite rem-fav-btn text-danger {{ $product_details->is_favorite == 0 ? 'd-none' : '' }}"
                                            data-product-id="{{ $product_details->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ labels('front_messages.remove_from_wishlist', 'Remove from Wishlist') }}">
                                            <i class="icon anm anm-heart fs-6"></i>
                                        </a>
                                        <a href="#"
                                            class="mq-icon-btn wishlist-submit add-favorite {{ $product_details->is_favorite == 0 ? '' : 'd-none' }}"
                                            data-product-id="{{ $product_details->id }}" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ labels('front_messages.add_to_wishlist', 'Add to Wishlist') }}">
                                            <i class="icon anm anm-heart-l fs-6"></i>
                                        </a>
                                        <a href="#" class="mq-icon-btn add-compare compare"
                                            data-product-id="{{ $product_details->id }}" data-product-type="regular" data-product-variant-id=""
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ labels('front_messages.add_to_compare', 'Add to Compare') }}">
                                            <i class="icon anm anm-random-r fs-6"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Seller Card -->
                                <div class="p-3 rounded-4 mt-3 d-flex align-items-center justify-content-between gap-2 flex-wrap"
                                    style="background: var(--mq-surface-alt); border: 1px solid var(--mq-border);">
                                    <div class="d-flex align-items-center gap-2 fw-semibold">
                                        <ion-icon name="storefront-outline" class="fs-5" style="color: var(--mq-pine);"></ion-icon>
                                        <a wire:navigate href="{{ customUrl('sellers/' . $product_details->seller_slug) }}"
                                            class="text-decoration-none" style="color: var(--mq-ink);">{{ $product_details->seller_name }}</a>
                                    </div>
                                    <span class="mq-rating-pill"><i class="bx bxs-star"></i> {{ $product_details->seller_rating }}</span>
                                </div>

                                <!-- Deliverability -->
                                @if ($product_details->product_type != 'digital_product' && $deliverabilitySettings != false)
                                    <div class="mt-3 pt-3 border-top">
                                        <p class="featureText fw-semibold small mb-2">
                                            {{ labels('front_messages.check_product_deliverability', 'Check Product Deliverability') }}
                                        </p>
                                        <div class="row g-2 align-items-center">
                                            @if ($deliverabilitySettings[0]->product_deliverability_type == 'city_wise_deliverability')
                                                <div class="col-md-9 city_list_div">
                                                    <select class="form-control city_list" id="city_list" name="city"></select>
                                                </div>
                                            @else
                                                <div class="col-md-9">
                                                    <input name="pincode" class="form-control"
                                                        placeholder="{{ labels('front_messages.enter_pincode', 'Enter Pincode') }}"
                                                        value="" id="pincode" type="text">
                                                </div>
                                            @endif
                                            <div class="col-md-3">
                                                <button type="submit" class="mq-btn-primary w-100 check-product-deliverability">
                                                    <span>{{ labels('front_messages.check', 'Check') }}</span>
                                                </button>
                                            </div>
                                            @if ($deliverabilitySettings[0]->product_deliverability_type == 'city_wise_deliverability')
                                                <p class="fw-400 text-danger small mb-0">
                                                    {{ labels('front_messages.city_not_on_list', 'If your city is not on the list') }}
                                                    {{ labels('front_messages.cannot_deliver', 'we cannot deliver the product there') }}.
                                                </p>
                                            @endif
                                            <p class="featureText deliverability-res mb-0 small"></p>
                                            <input type="hidden" name="product_deliverability_type" id="product_deliverability_type"
                                                value="{{ $deliverabilitySettings[0]->product_deliverability_type }}">
                                            <input type="hidden" name="product_id" id="product_id" value="{{ $product_id }}">
                                            <input type="hidden" name="product_type" id="product_type" value="regular">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($siblingsProduct['previous_product'] != null)
                        <a wire:navigate href="{{ customUrl('products/' . $siblingsProduct['previous_product']['slug']) }}"
                            class="product-nav prev-pro clr-none d-flex-center justify-content-between"
                            title="{{ labels('front_messages.previous_product', 'Previous Product') }}">
                            @php
                                $previous_product_img = app(MediaService::class)->dynamic_image($siblingsProduct['previous_product']['image'], 200);
                            @endphp
                            <span class="details">
                                <span class="name">{{ $siblingsProduct['previous_product']['name'] }}</span>
                                <span class="price">{{ app(CurrencyService::class)->currentCurrencyPrice((float) ($siblingsProduct['previous_product']['min_max_price']['special_min_price'] > 0 ? $siblingsProduct['previous_product']['min_max_price']['special_min_price'] : $siblingsProduct['previous_product']['min_max_price']['min_price']), true) }}</span>
                            </span>
                            <span class="img"><img src="{{ $previous_product_img }}" alt="{{ $siblingsProduct['previous_product']['name'] }}"></span>
                        </a>
                    @endif
                    @if ($siblingsProduct['next_product'] != null)
                        <a wire:navigate href="{{ customUrl('products/' . $siblingsProduct['next_product']['slug']) }}"
                            class="product-nav next-pro clr-none d-flex-center justify-content-between"
                            title="{{ labels('front_messages.next_product', 'Next Product') }}">
                            @php
                                $next_product_img = app(MediaService::class)->dynamic_image($siblingsProduct['next_product']['image'], 200);
                            @endphp
                            <span class="img"><img src="{{ $next_product_img }}" alt="{{ $siblingsProduct['next_product']['name'] }}"></span>
                            <span class="details">
                                <span class="name">{{ $siblingsProduct['next_product']['name'] }}</span>
                                <span class="price">{{ app(CurrencyService::class)->currentCurrencyPrice((float) ($siblingsProduct['next_product']['min_max_price']['special_min_price'] > 0 ? $siblingsProduct['next_product']['min_max_price']['special_min_price'] : $siblingsProduct['next_product']['min_max_price']['min_price']), true) }}</span>
                            </span>
                        </a>
                    @endif

                    <!-- Accordion -->
                    <div class="accordion mq-details-accordion mt-4" id="productAccordian">
                        @if ($product_details->description != '')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        {{ labels('front_messages.description', 'Description') }}
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#productAccordian">
                                    <div class="accordion-body product-description">
                                        {!! $product_details->description !!}
                                        @php
                                            $descText = trim(strip_tags((string) ($product_details->description ?? '')));
                                            $extraText = trim(strip_tags((string) ($product_details->extra_description ?? '')));
                                        @endphp
                                        @if ($extraText !== '' && $extraText !== $descText)
                                            <div class="mt-3">{!! $product_details->extra_description !!}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($product_details->attributes != [])
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        {{ labels('front_messages.additional_information', 'Additional Information') }}
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#productAccordian">
                                    <div class="accordion-body product-description" id="additionalInformation">
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle table-part mb-0">
                                                <tbody>
                                                    @foreach ($product_details->attributes as $attributes)
                                                        <tr>
                                                            <th>{{ $attributes['name'] }}</th>
                                                            <td>{{ $attributes['value'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    {{ labels('front_messages.reviews', 'Reviews') }}
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#productAccordian">
                                <div class="accordion-body" id="reviews">
                                    <livewire:pages.customer-ratings :product_id="$product_id" :product_details="$product_details" />
                                </div>
                            </div>
                        </div>

                        @if (isset($product_faqs['data']) && count($product_faqs['data']) >= 1)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFaqs">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseFaqs" aria-expanded="false" aria-controls="collapseFaqs">
                                        {{ labels('front_messages.faqs', 'FAQs') }}
                                    </button>
                                </h2>
                                <div id="collapseFaqs" class="accordion-collapse collapse" aria-labelledby="headingFaqs" data-bs-parent="#productAccordian">
                                    <div class="accordion-body" id="faqs">
                                        <div class="accordion accordion-flush" id="productFaqAccordion">
                                            @foreach ($product_faqs['data'] as $faq)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="faq-heading-{{ $faq['id'] }}">
                                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#faq-collapse-{{ $faq['id'] }}" aria-expanded="false"
                                                            aria-controls="faq-collapse-{{ $faq['id'] }}">
                                                            {{ $faq['question'] }}
                                                        </button>
                                                    </h2>
                                                    <div id="faq-collapse-{{ $faq['id'] }}" class="accordion-collapse collapse"
                                                        aria-labelledby="faq-heading-{{ $faq['id'] }}" data-bs-parent="#productFaqAccordion">
                                                        <div class="accordion-body">
                                                            {{ $faq['answer'] }}
                                                            @if (!empty($faq['answered_by']))
                                                                <div class="text-muted small mt-2">— {{ $faq['answered_by'] }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Trust & Related Sidebar -->
                <div class="col-lg-3 col-md-12 product-sidebar">
                    <div class="mq-details-trust-card mb-4">
                        @if ($product_details->product_type == 'digital_product')
                            <div class="d-flex align-items-center gap-2 fw-semibold">
                                <ion-icon name="cube-outline" class="fs-5" style="color: var(--mq-pine);"></ion-icon>
                                <span>{{ labels('front_messages.digital_product', 'Digital Product') }}</span>
                            </div>
                        @else
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <ion-icon name="ribbon-outline" class="fs-4 flex-shrink-0" style="color: var(--mq-pine);"></ion-icon>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ labels('front_messages.satisfaction_guarantee', 'Satisfaction Guarantee') }}</h6>
                                        <p class="small mb-0" style="color: var(--mq-muted);">
                                            {{ !empty($product_details->guarantee_period) ? $product_details->guarantee_period : labels('front_messages.no_guarantee', 'No Guarantee') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <ion-icon name="shield-checkmark-outline" class="fs-4 flex-shrink-0" style="color: var(--mq-pine);"></ion-icon>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ labels('front_messages.satisfaction_warranty', 'Satisfaction Warranty') }}</h6>
                                        <p class="small mb-0" style="color: var(--mq-muted);">
                                            {{ !empty($product_details->warranty_period) ? $product_details->warranty_period : labels('front_messages.no_warranty', 'No Warranty') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <ion-icon name="pin-outline" class="fs-4 flex-shrink-0" style="color: var(--mq-pine);"></ion-icon>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ labels('front_messages.cash_on_delivery', 'Cash On Delivery') }}</h6>
                                        <p class="small mb-0" style="color: var(--mq-muted);">
                                            {{ $product_details->cod_allowed == 1 ? labels('front_messages.cash_on_delivery_available', 'Cash on Delivery available') : labels('front_messages.cash_on_delivery_not_available', 'Cash on Delivery Not available') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <ion-icon name="refresh-outline" class="fs-4 flex-shrink-0" style="color: var(--mq-pine);"></ion-icon>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ labels('front_messages.returnable', 'Returnable') }}</h6>
                                        <p class="small mb-0" style="color: var(--mq-muted);">
                                            {{ $product_details->is_returnable == 1 ? labels('front_messages.returnable_30_days', 'Yes (30 Days)') : labels('front_messages.non_returnable', 'Non Returnable') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-start gap-3">
                                    <ion-icon name="close-circle-outline" class="fs-4 flex-shrink-0" style="color: var(--mq-pine);"></ion-icon>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ labels('front_messages.cancel_till', 'Cancel Till') }}</h6>
                                        <p class="small mb-0" style="color: var(--mq-muted);">
                                            {{ $product_details->is_cancelable == 1 ? $product_details->cancelable_till : labels('front_messages.non_cancelable', 'Non Cancelable') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if (count($relative_products) >= 1)
                        <div class="mq-details-trust-card">
                            <h5 class="fw-bold mb-3">{{ labels('front_messages.related_products', 'Related Products') }}</h5>
                            <div class="sideProSlider grid-products">
                                @foreach ($relative_products as $details)
                                    <div class="item">
                                        @php
                                            $store_settings = app(StoreService::class)->getStoreSettings();
                                            $component = getProductDisplayComponent($store_settings);
                                            $details = (object) $details;
                                        @endphp
                                        <x-dynamic-component :component="$component" :details="$details" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Cart -->
    <div class="stickyCart">
        <div class="mq-container">
            <div id="stickycart-form" class="d-flex-center justify-content-center">
                @php
                    $sticky_img = app(MediaService::class)->dynamic_image($product_details->image, 200);
                @endphp
                <div class="product-featured-img">
                    <img class="blur-up lazyload" data-src="{{ $sticky_img }}" src="{{ $sticky_img }}" width="120" height="170" />
                </div>
                <div class="sticky-title ms-2 ps-1 pe-5">{{ $product_details->name }}</div>
                <div class="stickyOptions position-relative">
                    <div class="selectedOpt sticky-cart-variant product_price">
                        @if ($product_details->type != 'variable_product')
                            {{ $special_price }}
                        @else
                            {{ $max_price }} - {{ $special_min_price }}
                        @endif
                    </div>
                </div>
                <div class="qtyField mx-2">
                    <button class="qtyBtn minus" href="#;"><ion-icon name="remove-outline"></ion-icon></button>
                    <input type="text" name="quantity" value="1" class="product-form-input qty dlt-qty"
                        max='{{ $product_details->total_allowed_quantity == 0 ? 'Infinity' : $product_details->total_allowed_quantity }}'
                        step='{{ $product_details->quantity_step_size }}'
                        min='{{ $product_details->minimum_order_quantity }}' />
                    <button class="qtyBtn plus" href="#;"><ion-icon name="add-outline"></ion-icon></button>
                </div>
                <button type="submit" name="add"
                    class="mq-btn-primary product-form-cart-submit add_cart dlt-add-cart"
                    data-product-reference-id="{{ isset($reference_id) && !empty($reference_id) ? $reference_id : '' }}"
                    data-product-variant-id="{{ $variant_id }}"
                    data-max='{{ $product_details->total_allowed_quantity }}'
                    data-step='{{ $product_details->quantity_step_size }}'
                    data-min='{{ $product_details->minimum_order_quantity }}'
                    data-store-id='{{ $product_details->store_id }}' data-variant-price="{{ $variant_price }}"
                    data-product-type='regular'>
                    <span>{{ labels('front_messages.add_to_cart', 'Add to cart') }}</span>
                </button>
            </div>
        </div>
        @php
            $system_settings = app(\App\Services\SettingService::class)->getSettings('system_settings', true);
            $system_settings = json_decode($system_settings, true);
            $scheme = str_replace('://', '', $system_settings['deep_link_scheme'] ?? 'muqtarin');
            $host = $system_settings['deep_link_host'] ?? 'muqtarin.store';
            $store_slug = session('store_slug') ?? ($product_details->store_slug ?? '');
            $deepLinkUrl = $scheme . '://' . $host . '/product/' . $product_details->slug . ($store_slug ? '?store=' . $store_slug : '');
        @endphp
        <script>
            var deepLinkUrl = "{{ $deepLinkUrl }}";
        </script>
        @include('partials.deep-link-bottom-sheet', ['deepLinkUrl' => $deepLinkUrl])
        <script>
            function toggleInput(liElement) {
                var inputElement = liElement.querySelector('input[type="radio"]');
                if (inputElement) {
                    inputElement.click();
                }
            }
        </script>
    </div>
</div>
