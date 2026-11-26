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
<div class="page-wrapper mq-page mq-theme-editorial">
    <div id="page-content" class="mb-0">
        <!-- Editorial Split Hero -->
        <section class="mq-hero">
            <div class="mq-container">
                <div class="mq-hero-editorial-grid">
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
                                                    <img src="{{ $slider['image'] }}" alt="Hero slide" />
                                                </a>
                                            @else
                                                <img src="{{ $slider['image'] }}" alt="Hero slide" />
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        @else
                            <img src="{{ asset('storage/system_images/theme_muqtarin_editorial.png') }}" alt="MUQTARIN Editorial" />
                        @endif
                    </div>

                    <div class="mq-hero-editorial-aside">
                        <div class="d-flex flex-column gap-3 position-relative" style="z-index: 2;">
                            <span class="mq-eyebrow"><i class="bx bxs-sparkles"></i> {{ labels('front_messages.curated_collection', 'Curated Collection') }}</span>
                            <h1 class="mq-hero-title">{{ $settings->site_title ?? 'MUQTARIN Store' }}</h1>
                            <p class="mq-hero-desc">{{ $settings->app_short_description ?? labels('front_messages.explore_categories', 'Discover handcrafted essentials, trusted brands, and effortless delivery.') }}</p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 position-relative" style="z-index: 2;">
                            <a wire:navigate href="{{ customUrl('products') }}" class="mq-btn-gold">
                                <span>{{ labels('front_messages.shop_now', 'Shop Now') }}</span>
                                <i class="bx bx-right-arrow-alt fs-5"></i>
                            </a>
                            <a wire:navigate href="{{ customUrl('offers') }}" class="mq-btn-ghost">
                                <span>{{ labels('front_messages.offers', 'Offers') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Pill Rail -->
        @if (is_array($cats) && count($cats) >= 1)
            <section class="mq-section pt-2">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ !empty($resolved_category_title) ? $resolved_category_title : labels('front_messages.popular_categories', 'Popular Categories') }}</h2>
                            <p class="mq-section-subtitle">{{ labels('front_messages.explore_categories', 'Explore top picks in our Categories!') }}</p>
                        </div>
                        <a wire:navigate href="{{ customUrl('categories') }}" class="mq-btn-ghost">
                            <span>{{ labels('front_messages.view_all', 'View All') }}</span>
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
                                    <span>{{ $category->product_count }} {{ labels('front_messages.products', 'Products') }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Sections -->
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
                                class="mq-btn-ghost">
                                <span>{{ labels('front_messages.explore_now', 'Explore Now') }}</span>
                            </a>
                        </div>

                        @if ($row->style == 'style_2' || $row->style == 'style_3')
                            <div class="row g-3 align-items-stretch">
                                <div class="col-12 col-lg-3 {{ $row->style == 'style_2' ? 'order-lg-2' : '' }}">
                                    <div class="mq-bento-tile mq-bento-tile--pine h-100 p-0">
                                        @if (!empty($row->banner_image))
                                            <img src="{{ $row->banner_image }}" alt="{{ $row->title }}"
                                                class="w-100 h-100 object-fit-cover" style="min-height: 240px; opacity: .35; position: absolute; inset: 0;">
                                        @endif
                                        <div class="position-relative p-4 d-flex flex-column justify-content-between h-100" style="z-index: 2;">
                                            <div>
                                                <span class="mq-eyebrow mb-2">{{ labels('front_messages.featured', 'Featured') }}</span>
                                                <h3 class="h4 fw-bold text-white mt-2">{{ $row->title }}</h3>
                                                <p class="small text-white-50">{{ $row->short_description }}</p>
                                            </div>
                                            <a wire:navigate
                                                href="{{ customUrl('section/' . $row->slug . '/' . $row->id . '/' . ($row->product_type == 'custom_combo_products' ? 'combo-' : '') . 'products') }}"
                                                class="mq-btn-gold align-self-start">
                                                {{ labels('front_messages.shop_now', 'Shop Now') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-9 {{ $row->style == 'style_2' ? 'order-lg-1' : '' }}">
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
                        @else
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
                        @endif
                    </div>
                </section>
            @endif
        @endforeach

        <!-- Offers & Marquee -->
        @if (isset($offers) && count($offers) > 0)
            <section class="mq-section pt-0">
                <div class="mq-container">
                    <div class="swiper mqOffersSwiper">
                        <div class="swiper-wrapper">
                            @foreach ($offers as $offer)
                                <div class="swiper-slide">
                                    <a href="{{ $offer->link }}"
                                        class="d-block rounded-4 overflow-hidden border {{ $offer->type != 'offer_url' ? 'slider-link' : '' }}"
                                        data-link="{{ $offer->link }}"
                                        {{ $offer->type == 'offer_url' ? 'target="_blank"' : 'wire:navigate' }}>
                                        <img src="{{ $offer->image }}" alt="{{ $offer->title }}" class="w-100 object-fit-cover" style="height: 210px;">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </section>

            <section class="mq-marquee-bar">
                <div class="marquee-text">
                    <div class="top-info-bar d-flex">
                        <div class="flex-item center">
                            @foreach ($offers as $offer)
                                <span><i class="bx bxs-offer me-1"></i>{{ $offer->title }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <!-- Blogs -->
        @if (isset($blogs) && count($blogs) > 0)
            <section class="mq-section">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ labels('front_messages.latest_from_our_blog', 'Latest from our Blog') }}</h2>
                            <p class="mq-section-subtitle">{{ labels('front_messages.top_news_stories_of_the_day', 'Top news stories of the day') }}</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        @foreach ($blogs->take(3) as $blog)
                            <div class="col-12 col-md-4">
                                <div class="mq-card h-100">
                                    <a wire:navigate href="{{ customUrl('blogs/' . $blog->slug) }}" class="mq-card-media" style="aspect-ratio: 16/10;">
                                        <img src="{{ asset('storage/' . $blog->image) }}" alt="{{ $blog->title }}">
                                    </a>
                                    <div class="mq-card-body">
                                        <span class="small" style="color: var(--mq-muted);">{{ $blog->created_at }}</span>
                                        <h3 class="mq-card-title">
                                            <a wire:navigate href="{{ customUrl('blogs/' . $blog->slug) }}">
                                                {!! Str::limit(app(TranslationService::class)->getDynamicTranslation(Blog::class, 'title', $blog->id, $language_code), 55) !!}
                                            </a>
                                        </h3>
                                        <p class="small mb-2" style="color: var(--mq-muted);">{!! Str::limit(strip_tags($blog->description), 90) !!}</p>
                                        <a wire:navigate href="{{ customUrl('blogs/' . $blog->slug) }}" class="mq-btn-ghost align-self-start mt-auto">
                                            {{ labels('front_messages.read_more', 'Read more') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <!-- Brands -->
        @if (is_array($brand_list) && count($brand_list) >= 1)
            <section class="mq-section pt-0">
                <div class="mq-container">
                    <div class="mq-section-head">
                        <div>
                            <h2 class="mq-section-title">{{ labels('front_messages.popular_brands', 'Popular Brands') }}</h2>
                            <p class="mq-section-subtitle">{{ labels('front_messages.explore_brands', 'Explore top picks in our Brands!') }}</p>
                        </div>
                        <a wire:navigate href="{{ customUrl('brands') }}" class="mq-btn-ghost">
                            <span>{{ labels('front_messages.view_all', 'View All') }}</span>
                        </a>
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
        mountSwiper('.mqOffersSwiper', {
            slidesPerView: 3, spaceBetween: 14,
            breakpoints: { 240: { slidesPerView: 1.15 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
        });
    })();
</script>
@endscript
