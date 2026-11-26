@php
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
<div class="page-wrapper mq-page mq-theme-express">
    <div id="page-content" class="mb-0">
        <!-- Express Compact Banner -->
        <section class="mq-hero pb-2">
            <div class="mq-container">
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
                                                <img src="{{ $slider['image'] }}" alt="Express banner" style="height: clamp(200px, 26vw, 340px);" />
                                            </a>
                                        @else
                                            <img src="{{ $slider['image'] }}" alt="Express banner" style="height: clamp(200px, 26vw, 340px);" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="swiper-pagination"></div>
                        </div>
                    @else
                        <img src="{{ asset('storage/system_images/theme_muqtarin_express.png') }}" alt="MUQTARIN Express" style="height: clamp(200px, 26vw, 340px);" />
                    @endif
                </div>
            </div>
        </section>

        <!-- Express Quick Category Pills -->
        @if (is_array($cats) && count($cats) >= 1)
            <section class="mq-section py-3">
                <div class="mq-container">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h2 class="h5 fw-bold m-0">{{ !empty($resolved_category_title) ? $resolved_category_title : labels('front_messages.popular_categories', 'Quick Categories') }}</h2>
                        <a wire:navigate href="{{ customUrl('categories') }}" class="small fw-semibold text-decoration-none d-inline-flex align-items-center gap-1" style="color: var(--mq-pine);">
                            {{ labels('front_messages.view_all', 'View All') }} <i class="bx bx-right-arrow-alt fs-6"></i>
                        </a>
                    </div>
                    <div class="mq-cat-pill-row">
                        @foreach ($cats as $category)
                            <a wire:navigate href="{{ customUrl('categories/' . $category->slug . '/products') }}"
                                class="mq-cat-pill-card slider-link"
                                data-link="{{ customUrl('categories/' . $category->slug . '/products') }}">
                                <img src="{{ $category->image }}" alt="{!! $category->name !!}" />
                                <div class="mq-cat-pill-meta">
                                    <strong>{!! $category->name !!}</strong>
                                    <span>{{ $category->product_count }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- High-Density Express Product Grids -->
        @foreach ($sections as $row)
            @if (!empty($row->product_details) && count((array) $row->product_details) > 0)
                <section class="mq-section py-3">
                    <div class="mq-container">
                        <div class="mq-section-head mb-3">
                            <div>
                                <h2 class="mq-section-title">{{ $row->title }}</h2>
                                @if (!empty($row->short_description))
                                    <p class="mq-section-subtitle">{{ $row->short_description }}</p>
                                @endif
                            </div>
                            <a wire:navigate
                                href="{{ customUrl('section/' . $row->slug . '/' . $row->id . '/' . ($row->product_type == 'custom_combo_products' ? 'combo-' : '') . 'products') }}"
                                class="mq-btn-primary py-2 px-3">
                                {{ labels('front_messages.view_all', 'See All') }}
                            </a>
                        </div>
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-3">
                            @foreach (array_slice((array) $row->product_details, 0, 12) as $details)
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
            <section class="mq-marquee-bar my-3">
                <div class="marquee-text">
                    <div class="top-info-bar d-flex">
                        <div class="flex-item center">
                            @foreach ($offers as $offer)
                                <span><i class="bx bxs-bolt me-1"></i>{{ $offer->title }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        @if (is_array($brand_list) && count($brand_list) >= 1)
            <section class="mq-section pt-2">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <h2 class="mq-section-title">{{ labels('front_messages.popular_brands', 'Popular Brands') }}</h2>
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
                    autoplay: { delay: 3800, disableOnInteraction: false },
                    pagination: pag ? { el: pag, clickable: true } : false
                });
            }
        });
    })();
</script>
@endscript
