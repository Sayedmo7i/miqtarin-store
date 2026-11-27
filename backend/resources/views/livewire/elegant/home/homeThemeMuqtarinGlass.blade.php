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
<div class="page-wrapper mq-page mq-theme-glass">
    <div id="page-content" class="mb-0">
        <!-- Glassmorphic Boutique Hero -->
        <section class="mq-hero">
            <div class="mq-container">
                <div class="mq-glass-panel p-3 p-md-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                            <span class="mq-eyebrow mq-eyebrow-light">
                                <i class="bx bxs-diamond"></i>
                                {{ labels('front_messages.boutique_experience', 'Glass Boutique') }}
                            </span>
                            <h1 class="mq-hero-title" style="color: var(--mq-ink);">{{ $language_code === 'ar' ? labels('front_messages.site_title', 'مقترن ستور') : ($settings->site_title ?? 'MUQTARIN Boutique') }}</h1>
                            <p class="mq-hero-desc" style="color: var(--mq-muted);">
                                {{ $language_code === 'ar' ? labels('front_messages.site_short_description', 'منصة تسوق متعددة التجار من مقترن ستور — توصيل سريع وجودة موثوقة.') : ($settings->app_short_description ?? labels('front_messages.explore_categories', 'Elevated shopping crafted with calm depth, crisp glass layers, and verified sellers.')) }}
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a wire:navigate href="{{ customUrl('products') }}" class="mq-btn-primary">
                                    <span>{{ labels('front_messages.shop_now', 'Shop Now') }}</span>
                                    <i class="bx bx-right-arrow-alt fs-5"></i>
                                </a>
                                <a wire:navigate href="{{ customUrl('sellers') }}" class="mq-btn-ghost">
                                    <span>{{ labels('front_messages.sellers', 'Sellers') }}</span>
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7">
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
                                                            <img src="{{ $slider['image'] }}" alt="Boutique slide" />
                                                        </a>
                                                    @else
                                                        <img src="{{ $slider['image'] }}" alt="Boutique slide" />
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="swiper-pagination"></div>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/system_images/theme_muqtarin_glass.png') }}" alt="MUQTARIN Glass" />
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Glass Rail -->
        @if (is_array($cats) && count($cats) >= 1)
            <section class="mq-section pt-2">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ !empty($resolved_category_title) ? $resolved_category_title : labels('front_messages.popular_categories', 'Popular Categories') }}</h2>
                            <p class="mq-section-subtitle">{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}</p>
                        </div>
                    </div>
                    <div class="mq-cat-pill-row">
                        @foreach ($cats as $category)
                            <a wire:navigate href="{{ customUrl('categories/' . $category->slug . '/products') }}"
                                class="mq-cat-pill-card slider-link"
                                data-link="{{ customUrl('categories/' . $category->slug . '/products') }}">
                                <img src="{{ $category->image }}" alt="{!! $category->name !!}" />
                                <div class="mq-cat-pill-meta">
                                    <strong>{!! $category->name !!}</strong>
                                    <span>{{ $category->product_count }} {{ labels('front_messages.products', 'Products') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Sections Inside Glass Panels -->
        @foreach ($sections as $row)
            @if (!empty($row->product_details) && count((array) $row->product_details) > 0)
                <section class="mq-section pt-2">
                    <div class="mq-container">
                        <div class="mq-glass-panel p-3 p-md-4">
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
                            <div class="swiper mqSectionSwiper">
                                <div class="swiper-wrapper">
                                    @foreach ($row->product_details as $details)
                                        <div class="swiper-slide h-auto">
                                            @php
                                                $component = getProductDisplayComponent($store_settings);
                                                $details = (object) $details;
                                            @endphp
                                            <x-dynamic-component :component="$component" :details="$details" />
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach

        @if (is_array($brand_list) && count($brand_list) >= 1)
            <section class="mq-section pt-2">
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
        function mountSwiper(selector, opts) {
            document.querySelectorAll(selector).forEach(function (el) {
                if (el.swiper) { try { el.swiper.destroy(true, true); } catch (e) {} }
                var cfg = Object.assign({}, opts);
                var nextEl = el.querySelector('.swiper-button-next');
                var prevEl = el.querySelector('.swiper-button-prev');
                if (nextEl && prevEl) cfg.navigation = { nextEl: nextEl, prevEl: prevEl };
                var pag = el.querySelector('.swiper-pagination');
                if (pag) cfg.pagination = { el: pag, clickable: true };
                if (el.querySelectorAll('.swiper-wrapper > .swiper-slide').length > 0) {
                    new Swiper(el, cfg);
                }
            });
        }
        mountSwiper('.mqHeroSwiper', { loop: false, autoplay: { delay: 4200, disableOnInteraction: false } });
        mountSwiper('.mqSectionSwiper', {
            slidesPerView: 4, spaceBetween: 16,
            breakpoints: { 240: { slidesPerView: 1.35, spaceBetween: 12 }, 576: { slidesPerView: 2, spaceBetween: 14 }, 992: { slidesPerView: 3, spaceBetween: 16 }, 1200: { slidesPerView: 4, spaceBetween: 18 } }
        });
    })();
</script>
@endscript
