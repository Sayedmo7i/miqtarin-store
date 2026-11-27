@php
    use App\Models\Blog;
    use App\Services\TranslationService;
    use App\Services\StoreService;
    $store_settings = app(StoreService::class)->getStoreSettings();
    $language_code = app(TranslationService::class)->getLanguageCode();
    $category_section_title = $store_settings['category_section_title'] ?? 'Categories';
    $resolved_category_title = is_array($category_section_title)
        ? ($category_section_title[$language_code] ?? null)
        : $category_section_title;
    $cats = $categories['categories'] ?? [];
    $brand_list = $brands['brands'] ?? [];
@endphp
<div class="page-wrapper mq-page mq-theme-bento">
    <div id="page-content" class="mb-0">
        <!-- Bento Mosaic Hero -->
        <section class="mq-hero">
            <div class="mq-container">
                <div class="mq-bento-hero-grid">
                    <div class="mq-hero-slider-card">
                        @if (!empty($sliders) && count($sliders) > 0)
                            <div class="swiper mqHeroSwiper">
                                <div class="swiper-wrapper">
                                    @foreach ($sliders as $slider)
                                        <div class="swiper-slide">
                                            @if ($slider['type'] !== 'default')
                                                <a @if ($slider['type'] !== 'slider_url') wire:navigate @endif href="{{ $slider['link'] }}"
                                                    class="slider-link d-block" data-link="{{ $slider['link'] }}"
                                                    target="{{ $slider['type'] == 'slider_url' ? '_blank' : '' }}">
                                                    <img src="{{ $slider['image'] }}" alt="Bento slide" />
                                                </a>
                                            @else
                                                <img src="{{ $slider['image'] }}" alt="Bento slide" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <img src="{{ asset('storage/system_images/theme_muqtarin_bento.png') }}" alt="MUQTARIN Bento" />
                        @endif
                    </div>

                    <div class="mq-bento-side-stack">
                        <div class="mq-bento-tile mq-bento-tile--pine">
                            <div>
                                <span class="mq-eyebrow"><i class="bx bxs-grid-alt"></i> {{ labels('front_messages.popular_categories', 'Bento Collections') }}</span>
                                <h2 class="h4 fw-bold text-white mt-3 mb-1">{{ $language_code === 'ar' ? labels('front_messages.site_title', 'مقترن ستور') : ($settings->site_title ?? 'MUQTARIN Store') }}</h2>
                                <p class="small text-white-50 mb-0">{{ count($cats) }} {{ labels('front_messages.categories', 'Categories') }} &middot; {{ count($brand_list) }} {{ labels('front_messages.brands', 'Brands') }}</p>
                            </div>
                            <a wire:navigate href="{{ customUrl('products') }}" class="mq-btn-gold align-self-start mt-3">
                                {{ labels('front_messages.shop_now', 'Shop Now') }}
                            </a>
                        </div>

                        <div class="mq-bento-tile">
                            <div>
                                <span class="mq-eyebrow mq-eyebrow-light"><i class="bx bxs-bolt"></i> {{ labels('front_messages.combo_products', 'Combo Deals') }}</span>
                                <h3 class="h5 fw-bold mt-2 mb-1" style="color: var(--mq-ink);">{{ labels('front_messages.offers', 'Special Offers & Bundles') }}</h3>
                                <p class="small mb-0" style="color: var(--mq-muted);">{{ labels('front_messages.explore_categories', 'Curated multi-store savings updated daily.') }}</p>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a wire:navigate href="{{ customUrl('combo-products') }}" class="mq-btn-primary">
                                    {{ labels('front_messages.combo_products', 'Combos') }}
                                </a>
                                <a wire:navigate href="{{ customUrl('offers') }}" class="mq-btn-ghost">
                                    {{ labels('front_messages.offers', 'Offers') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Bento Categories Grid -->
        @if (is_array($cats) && count($cats) >= 1)
            <section class="mq-section pt-2">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ !empty($resolved_category_title) ? $resolved_category_title : labels('front_messages.popular_categories', 'Popular Categories') }}</h2>
                            <p class="mq-section-subtitle">{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}</p>
                        </div>
                        <a wire:navigate href="{{ customUrl('categories') }}" class="mq-btn-ghost">{{ labels('front_messages.view_all', 'View All') }}</a>
                    </div>
                    <div class="mq-cat-bento-grid">
                        @foreach (array_slice($cats, 0, 6) as $category)
                            <a wire:navigate href="{{ customUrl('categories/' . $category->slug . '/products') }}"
                                class="mq-cat-bento-card slider-link"
                                data-link="{{ customUrl('categories/' . $category->slug . '/products') }}">
                                <div class="mq-cat-bento-media">
                                    <img src="{{ $category->image }}" alt="{!! $category->name !!}" />
                                </div>
                                <div class="mq-cat-bento-body">
                                    <div>
                                        <h3>{!! $category->name !!}</h3>
                                        <span class="small" style="color: var(--mq-muted);">{{ $category->product_count }} {{ labels('front_messages.products', 'Products') }}</span>
                                    </div>
                                    <span class="mq-cat-bento-arrow"><i class="bx bx-chevron-right fs-5"></i></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Sections Bento Grid -->
        @foreach ($sections as $row)
            @if (!empty($row->product_details) && count((array) $row->product_details) > 0)
                <section class="mq-section">
                    <div class="mq-container">
                        <div class="mq-section-head">
                            <div>
                                <h2 class="mq-section-title">{{ $row->title }}</h2>
                                @if (!empty($row->short_description))
                                    <p class="mq-section-subtitle">{{ $row->short_description }}</p>
                                @endif
                            </div>
                            <a wire:navigate
                                href="{{ customUrl('section/' . $row->slug . '/' . $row->id . '/' . ($row->product_type == 'custom_combo_products' ? 'combo-' : '') . 'products') }}"
                                class="mq-btn-ghost">{{ labels('front_messages.explore_now', 'Explore Now') }}</a>
                        </div>
                        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
                            @foreach (array_slice((array) $row->product_details, 0, 10) as $details)
                                <div class="col">
                                    @php
                                        $component = getProductDisplayComponent($store_settings);
                                        $details = (object) $details;
                                    @endphp
                                    <x-dynamic-component :component="$component" :details="$details" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif
        @endforeach

        @if (isset($offers) && count($offers) > 0)
            <section class="mq-section pt-0">
                <div class="mq-container">
                    <div class="row g-3">
                        @foreach (collect($offers)->take(3) as $offer)
                            <div class="col-12 col-md-4">
                                <a href="{{ $offer->link }}"
                                    class="mq-bento-tile p-0 d-block {{ $offer->type != 'offer_url' ? 'slider-link' : '' }}"
                                    data-link="{{ $offer->link }}"
                                    {{ $offer->type == 'offer_url' ? 'target="_blank"' : 'wire:navigate' }}>
                                    <img src="{{ $offer->image }}" alt="{{ $offer->title }}" class="w-100 object-fit-cover" style="height: 200px;">
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (is_array($brand_list) && count($brand_list) >= 1)
            <section class="mq-section pt-0">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ labels('front_messages.popular_brands', 'Popular Brands') }}</h2>
                        </div>
                        <a wire:navigate href="{{ customUrl('brands') }}" class="mq-btn-ghost">{{ labels('front_messages.view_all', 'View All') }}</a>
                    </div>
                    <div class="mq-brands-grid">
                        @foreach (array_slice($brand_list, 0, 8) as $brand)
                            <a wire:navigate href="{{ customUrl('products/?brand=' . $brand['brand_slug']) }}" class="mq-brand-card slider-link"
                                data-link="{{ customUrl('products/?brand=' . $brand['brand_slug']) }}">
                                <div class="mq-brand-logo-box">
                                    <img src="{{ $brand['brand_img'] }}" alt="{!! $brand['brand_name'] !!}" />
                                </div>
                                <h4 class="mq-brand-name">{!! $brand['brand_name'] !!}</h4>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <x-utility.safety_and_security.styleThree :$settings />
    </div>
</div>

@script
<script>
    (function () {
        if (typeof Swiper === 'undefined') return;
        document.querySelectorAll('.mqHeroSwiper').forEach(function (el) {
            if (el.swiper) { try { el.swiper.destroy(true, true); } catch (e) {} }
            var pag = el.querySelector('.swiper-pagination');
            if (el.querySelectorAll('.swiper-wrapper > .swiper-slide').length > 0) {
                new Swiper(el, {
                    loop: false,
                    autoplay: { delay: 4200, disableOnInteraction: false },
                    pagination: pag ? { el: pag, clickable: true } : false
                });
            }
        });
    })();
</script>
@endscript
