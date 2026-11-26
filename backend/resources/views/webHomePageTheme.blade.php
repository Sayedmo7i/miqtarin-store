<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page Themes - Showcase Only</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/plugins.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/style.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/app.css') }}?v={{ $version }}">
    <link rel="stylesheet" href="{{ asset('frontend/elegant/css/swiper-bundle.min.css') }}?v={{ $version }}">
    @include('partials.webShowcaseBridge')
    <style>
        .mq-mini-shell {
            border-radius: 18px;
            border: 1px solid var(--mq-border);
            background: var(--mq-canvas);
            overflow: hidden;
            box-shadow: 0 14px 36px -14px rgba(15, 23, 42, 0.16);
        }
        .mq-mini-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            background: var(--mq-surface);
            border-bottom: 1px solid var(--mq-border);
            font-size: 0.78rem;
            font-weight: 700;
        }
        .mq-mini-topbar--glass {
            background: rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(12px);
        }
        html[data-theme="dark"] .mq-mini-topbar--glass {
            background: rgba(18, 28, 36, 0.78);
        }
        .mq-mini-nav {
            display: flex;
            gap: 14px;
            font-weight: 600;
            color: var(--mq-muted);
            font-size: 0.74rem;
        }
        .mq-mini-body {
            padding: 18px;
        }
        .mq-mini-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 14px;
        }
        .mq-mini-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-top: 14px;
        }
        @media (max-width: 640px) {
            .mq-mini-grid-3, .mq-mini-grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .mq-bento-hero-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>

<body>
    <div class="mq-showcase-stage">
        {{-- MUQTARIN 1: Editorial Pine --}}
        <div id="web_home_page_theme_muqtarin_editorial" class="content-section">
            <div class="mq-mini-shell mq-theme-editorial">
                <div class="mq-mini-topbar">
                    <span class="d-inline-flex align-items-center gap-2" style="color: var(--mq-pine);">
                        <i class="bx bxs-leaf fs-5"></i>
                        <span data-en="MUQTARIN Editorial" data-ar="مقترن التحريري">MUQTARIN Editorial</span>
                    </span>
                    <div class="mq-mini-nav d-none d-sm-flex">
                        <span data-en="Home" data-ar="الرئيسية">Home</span>
                        <span data-en="Curated" data-ar="المختارات">Curated</span>
                        <span data-en="Categories" data-ar="الفئات">Categories</span>
                        <span data-en="Brands" data-ar="الماركات">Brands</span>
                    </div>
                    <span class="mq-legacy-pill" data-en="2-Col Hero + Cards" data-ar="واجهة تحريرية">2-Col Hero + Cards</span>
                </div>
                <div class="mq-mini-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <span class="mq-eyebrow mq-eyebrow-light mb-2"><i class="bx bxs-sparkles"></i> <span data-en="Curated Collection" data-ar="تشكيلة مختارة">Curated Collection</span></span>
                            <h3 class="fw-bold mb-2" style="font-size:1.35rem;color:var(--mq-ink);" data-en="Modern Commerce, Craft & Clarity" data-ar="تجارة عصرية بهوية هادئة وأنيقة">Modern Commerce, Craft &amp; Clarity</h3>
                            <p class="small mb-3" style="color:var(--mq-muted);" data-en="Editorial split hero with emerald badges, spacious product cards, and rhythmic category grids." data-ar="واجهة متجر تحريرية بتصميم ثنائي الأعمدة وبطاقات منتجات رحبة وخطوط عربية متناسقة.">Editorial split hero with emerald badges, spacious product cards, and rhythmic category grids.</p>
                            <div class="d-flex gap-2">
                                <span class="mq-btn-primary btn-sm px-3 py-1" data-en="Shop Now" data-ar="تسوّق الآن">Shop Now <i class="bx bx-right-arrow-alt"></i></span>
                                <span class="mq-btn-ghost btn-sm px-3 py-1" data-en="Categories" data-ar="تصفّح الفئات">Categories</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <img src="{{ asset('storage/system_images/theme_muqtarin_editorial.png') }}" alt="MUQTARIN Editorial" class="w-100 rounded-3 border" style="max-height:220px;object-fit:cover;">
                        </div>
                    </div>
                    <div class="mq-mini-grid-3">
                        <div class="mq-card p-2">
                            <div class="mq-card-body p-2">
                                <span class="mq-badge-discount">-20%</span>
                                <div class="fw-bold small mt-1" data-en="Artisan Brew" data-ar="قهوة مختصة">Artisan Brew</div>
                                <div class="small" style="color:var(--mq-pine);font-weight:700;">$28.00</div>
                            </div>
                        </div>
                        <div class="mq-card p-2">
                            <div class="mq-card-body p-2">
                                <span class="mq-eyebrow mq-eyebrow-light" data-en="Featured" data-ar="مميز">Featured</span>
                                <div class="fw-bold small mt-1" data-en="Ceramic Set" data-ar="طقم خزفي">Ceramic Set</div>
                                <div class="small" style="color:var(--mq-pine);font-weight:700;">$44.00</div>
                            </div>
                        </div>
                        <div class="mq-card p-2">
                            <div class="mq-card-body p-2">
                                <span class="mq-eyebrow mq-eyebrow-light" data-en="New" data-ar="جديد">New</span>
                                <div class="fw-bold small mt-1" data-en="Linen Throw" data-ar="غطاء كتان">Linen Throw</div>
                                <div class="small" style="color:var(--mq-pine);font-weight:700;">$36.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MUQTARIN 2: Bento Market --}}
        <div id="web_home_page_theme_muqtarin_bento" class="content-section d-none">
            <div class="mq-mini-shell mq-theme-bento">
                <div class="mq-mini-topbar">
                    <span class="d-inline-flex align-items-center gap-2" style="color: var(--mq-pine);">
                        <i class="bx bxs-grid-alt fs-5"></i>
                        <span data-en="MUQTARIN Bento Market" data-ar="سوق بنتو المقترن">MUQTARIN Bento Market</span>
                    </span>
                    <span class="mq-legacy-pill" data-en="12-Col Bento Grid" data-ar="شبكة بنتو حديثة">12-Col Bento Grid</span>
                </div>
                <div class="mq-mini-body">
                    <div class="mq-bento-hero-grid" style="gap:12px;">
                        <div class="mq-hero-slider-card" style="min-height:220px;">
                            <img src="{{ asset('storage/system_images/theme_muqtarin_bento.png') }}" alt="MUQTARIN Bento" style="height:220px;width:100%;object-fit:cover;" />
                        </div>
                        <div class="mq-bento-side-stack" style="gap:10px;">
                            <div class="mq-bento-tile mq-bento-tile--pine p-3">
                                <span class="mq-eyebrow"><i class="bx bxs-grid-alt"></i> <span data-en="Bento Spotlight" data-ar="واجهة بنتو">Bento Spotlight</span></span>
                                <div class="fw-bold text-white mt-2" data-en="Curated Multi-Vendor" data-ar="تشكيلة متاجر متعددة">Curated Multi-Vendor</div>
                                <span class="mq-btn-gold btn-sm align-self-start mt-2" data-en="Shop Now" data-ar="تسوّق">Shop Now</span>
                            </div>
                            <div class="mq-bento-tile p-3">
                                <span class="mq-eyebrow mq-eyebrow-light"><i class="bx bxs-bolt"></i> <span data-en="Bundles" data-ar="باقات">Bundles</span></span>
                                <div class="fw-bold small mt-1" style="color:var(--mq-ink);" data-en="Daily Combo Offers" data-ar="عروض يومية مجمعة">Daily Combo Offers</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MUQTARIN 3: Glass Atelier --}}
        <div id="web_home_page_theme_muqtarin_glass" class="content-section d-none">
            <div class="mq-mini-shell mq-theme-glass">
                <div class="mq-mini-topbar mq-mini-topbar--glass">
                    <span class="d-inline-flex align-items-center gap-2" style="color: var(--mq-pine);">
                        <i class="bx bxs-diamond fs-5"></i>
                        <span data-en="MUQTARIN Glass Atelier" data-ar="أتيليه الزجاجي">MUQTARIN Glass Atelier</span>
                    </span>
                    <span class="mq-legacy-pill" data-en="Frosted Glass Surface" data-ar="أسطح زجاجية شفافة">Frosted Glass Surface</span>
                </div>
                <div class="mq-mini-body">
                    <div class="position-relative rounded-4 overflow-hidden border" style="border-color:var(--mq-border)!important;">
                        <img src="{{ asset('storage/system_images/theme_muqtarin_glass.png') }}" alt="MUQTARIN Glass" style="width:100%;height:290px;object-fit:cover;">
                        <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background:linear-gradient(180deg,transparent,rgba(11,25,21,0.78));">
                            <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background:rgba(255,255,255,0.16);backdrop-filter:blur(10px);color:#fff;border:1px solid rgba(255,255,255,0.22);">
                                <i class="bx bxs-star text-warning"></i>
                                <span class="small fw-semibold" data-en="Boutique Experience · Translucent Header & Floating Cards" data-ar="تجربة راقية · شريط علوي شفاف وبطاقات عائمة">Boutique Experience · Translucent Header &amp; Floating Cards</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MUQTARIN 4: Fresh Express --}}
        <div id="web_home_page_theme_muqtarin_express" class="content-section d-none">
            <div class="mq-mini-shell mq-theme-express">
                <div class="mq-mini-topbar">
                    <span class="d-inline-flex align-items-center gap-2" style="color: var(--mq-pine);">
                        <i class="bx bxs-bolt-circle fs-5"></i>
                        <span data-en="MUQTARIN Fresh Express" data-ar="إكسبريس السريع">MUQTARIN Fresh Express</span>
                    </span>
                    <span class="mq-legacy-pill" data-en="High-Density SKU Grid" data-ar="شبكة تسوق سريعة">High-Density SKU Grid</span>
                </div>
                <div class="mq-mini-body">
                    <img src="{{ asset('storage/system_images/theme_muqtarin_express.png') }}" alt="MUQTARIN Express" class="w-100 rounded-3 border" style="max-height:175px;object-fit:cover;">
                    <div class="mq-mini-grid-4">
                        @for ($i = 1; $i <= 4; $i++)
                            <div class="mq-card mq-card--express p-2">
                                <div class="small fw-bold" style="color:var(--mq-ink);" data-en="Express SKU #{{ $i }}" data-ar="منتج سريع #{{ $i }}">Express SKU #{{ $i }}</div>
                                <div class="d-flex align-items-center justify-content-between mt-2">
                                    <span class="small fw-bold" style="color:var(--mq-pine);">${{ 10 + $i * 4 }}.00</span>
                                    <span class="badge rounded-pill" style="background:var(--mq-pine);color:#fff;">+</span>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        @php
            $legacyThemes = [
                'web_home_page_theme_1' => ['img' => 'theme_1.png', 'en' => 'Classic Fashion Storefront', 'ar' => 'الواجهة الكلاسيكية للأزياء', 'tag_en' => 'Legacy Theme 1', 'tag_ar' => 'القالب الأساسي 1'],
                'web_home_page_theme_2' => ['img' => 'theme_2.png', 'en' => 'Prime Pantry Supermarket', 'ar' => 'واجهة البقالة والسوبرماركت', 'tag_en' => 'Legacy Theme 2', 'tag_ar' => 'القالب الأساسي 2'],
                'web_home_page_theme_3' => ['img' => 'theme_3.png', 'en' => 'Luxeline Editorial Commerce', 'ar' => 'واجهة لاكس لاين الفاخرة', 'tag_en' => 'Legacy Theme 3', 'tag_ar' => 'القالب الأساسي 3'],
                'web_home_page_theme_4' => ['img' => 'theme_4.png', 'en' => 'Modern Pharmacy & Wellness', 'ar' => 'واجهة الصيدلية والعناية', 'tag_en' => 'Legacy Theme 4', 'tag_ar' => 'القالب الأساسي 4'],
                'web_home_page_theme_5' => ['img' => 'theme_5.png', 'en' => 'Electronics & Tech Hub', 'ar' => 'واجهة الإلكترونيات والتقنية', 'tag_en' => 'Legacy Theme 5', 'tag_ar' => 'القالب الأساسي 5'],
                'web_home_page_theme_6' => ['img' => 'theme_6.png', 'en' => 'Fresh Grocery & Market', 'ar' => 'واجهة المتجر الغذائي', 'tag_en' => 'Legacy Theme 6', 'tag_ar' => 'القالب الأساسي 6'],
            ];
        @endphp

        @foreach ($legacyThemes as $id => $meta)
            <div id="{{ $id }}" class="content-section d-none">
                <div class="mq-legacy-frame">
                    <div class="mq-legacy-frame-head">
                        <div class="mq-legacy-frame-title">
                            <i class="bx bx-layout fs-5" style="color: var(--mq-pine);"></i>
                            <span data-en="{{ $meta['en'] }}" data-ar="{{ $meta['ar'] }}">{{ $meta['en'] }}</span>
                        </div>
                        <span class="mq-legacy-pill" data-en="{{ $meta['tag_en'] }}" data-ar="{{ $meta['tag_ar'] }}">{{ $meta['tag_en'] }}</span>
                    </div>
                    <img src="{{ asset('storage/system_images/' . $meta['img']) }}" alt="{{ $meta['en'] }}">
                </div>
            </div>
        @endforeach
    </div>
</body>

<script src="{{ asset('frontend/elegant/js/plugins.js') }}?v={{ $version }}"></script>
<script src="{{ asset('frontend/elegant/js/swiper-bundle.min.js') }}?v={{ $version }}"></script>
</html>
