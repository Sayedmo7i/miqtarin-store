<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mo7i Store - الهوية البصرية ونظام التصميم الموحد</title>
    
    <!-- Fonts: Cairo for Arabic & Rubik for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Rubik:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            /* Brand Colors */
            --color-primary: #B52046;
            --color-primary-hover: #961837;
            --color-primary-light: #FDF2F4;
            --color-secondary: #201A1A;
            --color-secondary-surface: #2A2424;

            /* Functional / Status */
            --color-success: #0DAE4A;
            --color-success-light: #E7F8EE;
            --color-pending: #E69914;
            --color-pending-light: #FEF7E8;
            --color-received: #4285F4;
            --color-processed: #736AFF;
            --color-shipped: #C44D6B;
            --color-error: #EE1C2F;
            --color-error-light: #FDE8EA;
            --color-info: #2196F3;

            /* Neutrals */
            --neutral-50: #F8FAFC;
            --neutral-100: #F1F5F9;
            --neutral-200: #E2E8F0;
            --neutral-300: #CBD5E1;
            --neutral-400: #94A3B8;
            --neutral-500: #64748B;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1E293B;
            --neutral-900: #0F172A;

            /* Typography */
            --font-ar: 'Cairo', sans-serif;
            --font-en: 'Rubik', sans-serif;
            --font-code: 'JetBrains Mono', monospace;

            /* Spacing */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-full: 9999px;

            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px rgba(0,0,0,0.1);
            --shadow-brand: 0 8px 24px rgba(181, 32, 70, 0.25);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-ar);
            background-color: var(--neutral-50);
            color: var(--neutral-900);
            line-height: 1.6;
            padding-bottom: 80px;
        }

        /* Top Navigation Header */
        .header {
            background: #ffffff;
            border-bottom: 1px solid var(--neutral-200);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(8px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 600;
        }

        .badge-brand {
            background-color: var(--color-primary-light);
            color: var(--color-primary);
            border: 1px solid rgba(181, 32, 70, 0.2);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Hero Banner */
        .hero {
            background: linear-gradient(135deg, #201A1A 0%, #361720 100%);
            color: #ffffff;
            padding: 60px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: 50%;
            transform: translateX(-50%);
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(181, 32, 70, 0.3) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-title {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .hero-title span {
            color: #FF5A7D;
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--neutral-300);
            max-width: 700px;
            margin: 0 auto 28px auto;
        }

        /* Container & Grid */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        .section-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--color-secondary);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-desc {
            color: var(--neutral-500);
            font-size: 15px;
            margin-bottom: 28px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 48px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 48px;
        }

        /* Cards */
        .card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--neutral-200);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            border-color: var(--neutral-300);
        }

        /* Asset Display Box */
        .asset-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            background: #ffffff;
            border: 1px solid var(--neutral-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .asset-preview {
            width: 100%;
            height: 240px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            border-bottom: 1px solid var(--neutral-200);
        }

        .asset-preview.dark {
            background: #141112;
        }

        .asset-preview img, .asset-preview svg {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .asset-info {
            padding: 20px;
            width: 100%;
            text-align: right;
        }

        .asset-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--color-secondary);
            margin-bottom: 6px;
        }

        .asset-meta {
            font-size: 13px;
            color: var(--neutral-500);
            font-family: var(--font-code);
            direction: ltr;
            text-align: left;
            background: var(--neutral-100);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            margin-top: 8px;
            word-break: break-all;
        }

        /* Color Swatches */
        .color-swatch-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--neutral-200);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: transform 0.15s ease;
        }

        .color-swatch-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .color-display {
            height: 120px;
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
            padding: 12px;
            color: #ffffff;
            font-family: var(--font-code);
            font-size: 14px;
            font-weight: 600;
        }

        .color-info {
            padding: 16px;
        }

        .color-name {
            font-weight: 700;
            font-size: 16px;
            color: var(--color-secondary);
            margin-bottom: 4px;
        }

        .color-hex {
            font-family: var(--font-code);
            font-size: 13px;
            color: var(--neutral-500);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .color-usage {
            font-size: 12px;
            color: var(--neutral-500);
            margin-top: 8px;
            line-height: 1.4;
        }

        /* Typography specimens */
        .type-specimen {
            border-bottom: 1px solid var(--neutral-200);
            padding: 16px 0;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        .type-specimen:last-child {
            border-bottom: none;
        }

        .type-meta {
            font-family: var(--font-code);
            font-size: 12px;
            color: var(--neutral-500);
            direction: ltr;
        }

        /* Buttons & Components */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-family: var(--font-ar);
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            box-shadow: var(--shadow-brand);
        }

        .btn-primary:hover {
            background-color: var(--color-primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: var(--color-secondary);
            color: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #361720;
        }

        .btn-outline {
            background-color: transparent;
            color: var(--color-primary);
            border: 2px solid var(--color-primary);
        }

        .btn-outline:hover {
            background-color: var(--color-primary-light);
        }

        /* Code block for AI agents */
        .code-container {
            background: #141112;
            color: #f1f5f9;
            padding: 24px;
            border-radius: var(--radius-lg);
            font-family: var(--font-code);
            font-size: 13px;
            line-height: 1.6;
            direction: ltr;
            text-align: left;
            overflow-x: auto;
            border: 1px solid #332629;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: #2a2023;
            color: #fff;
            border: 1px solid #4d3339;
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            cursor: pointer;
            font-family: var(--font-ar);
        }

        .copy-btn:hover {
            background: var(--color-primary);
        }

        /* Toast feedback */
        #toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--color-secondary);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 600;
            display: none;
            z-index: 9999;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(255,255,255,0.15);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="brand-logo-wrap">
                <img src="{{ asset('assets/brand/logo.svg') }}" alt="Mo7i Store Logo" class="brand-logo-img">
                <span class="badge-status badge-brand">
                    <i class='bx bxs-check-shield'></i> نظام الهوية المعتمد
                </span>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.login') }}" class="btn btn-outline" style="padding: 8px 16px; font-size: 13px;">
                    <i class='bx bx-lock-alt'></i> لوحة التحكم
                </a>
                <a href="{{ url('/') }}" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px;">
                    <i class='bx bx-store-alt'></i> المتجر الرئيسي
                </a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <h1 class="hero-title">دليل الهوية البصرية ونظام التصميم لـ <span>Mo7i Store</span></h1>
        <p class="hero-subtitle">
            الدليل الشامل والمرجع الرسمي (Single Source of Truth) لكافة المطورين وروبوتات الذكاء الاصطناعي (AI Agents) لتوحيد التصميم عبر الويب، لوحة الإدارة، وتطبيقات الجوال.
        </p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <a href="#logos" class="btn btn-primary"><i class='bx bx-images'></i> استعراض الشعارات</a>
            <a href="#colors" class="btn btn-secondary"><i class='bx bx-palette'></i> رموز الألوان (Tokens)</a>
            <a href="#agents" class="btn btn-outline" style="color: #fff; border-color: #fff;"><i class='bx bx-bot'></i> قواعد الـ AI Agents</a>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="container">

        <!-- 1. Brand Logos & Assets -->
        <section id="logos" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-crown' style="color: var(--color-primary)"></i> 1. الشعارات الرسمية والأيقونات (Brand Assets)</h2>
            <p class="section-desc">أصول بصرية منتجة هندسياً وبتقنية الذكاء الاصطناعي (Nano Banana Pro) بصيغ متجهة فائقة الوضوح (SVG) ومحفوظة بمجلد موحد.</p>

            <div class="grid-2">
                <!-- Vector Master Logo -->
                <div class="asset-card">
                    <div class="asset-preview">
                        <img src="{{ asset('assets/brand/logo.svg') }}" alt="Mo7i Store Vector Logo">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">الشعار الرئيسي المتجه (Vector Master Logo)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">الشعار الرسمي للهيدر، الفواتير، التقارير، والطباعة. يدعم القياس اللانهائي دون أي بكسلة.</p>
                        <div class="asset-meta">/assets/brand/logo.svg</div>
                    </div>
                </div>

                <!-- App Icon Vector -->
                <div class="asset-card">
                    <div class="asset-preview dark">
                        <img src="{{ asset('assets/brand/icon.svg') }}" alt="Mo7i Store Vector App Icon" style="width: 140px; height: 140px;">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">أيقونة التطبيقات (App Icon Squircle)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">الأيقونة المعتمدة لتطبيقات Flutter (العميل، التاجر، المندوب) والـ Favicon مع تأثير التوهج القرمزي.</p>
                        <div class="asset-meta">/assets/brand/icon.svg</div>
                    </div>
                </div>

                <!-- AI Generated Brand Art -->
                <div class="asset-card">
                    <div class="asset-preview">
                        <img src="{{ asset('assets/brand/logo.jpg') }}" alt="Mo7i Store Generated Logo">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">شعار الذكاء الاصطناعي التجريبي (AI Visual Model)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">تم إنشاؤه عبر نانو بنانا المدمج لتجسيد الدمج بين حرف M وحقيبة التسوق العصرية.</p>
                        <div class="asset-meta">/assets/brand/logo.jpg</div>
                    </div>
                </div>

                <!-- AI Generated Dark App Icon -->
                <div class="asset-card">
                    <div class="asset-preview dark">
                        <img src="{{ asset('assets/brand/icon.jpg') }}" alt="Mo7i Store App Icon Art" style="border-radius: 24px;">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">أيقونة النيون الداكنة (Dark Neon Monogram)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">تصميم فخم لشاشات البداية (Splash Screens) وحملات السوشيال ميديا الداكنة.</p>
                        <div class="asset-meta">/assets/brand/icon.jpg</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Color Palette & Tokens -->
        <section id="colors" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-palette' style="color: var(--color-primary)"></i> 2. لوحة الألوان ورموز التصميم (Color Tokens)</h2>
            <p class="section-desc">اضغط على أي كارت لنسخ رمز اللون الست عشري (Hex Code) أو كود Flutter مباشرة.</p>

            <h3 style="font-size: 18px; margin-bottom: 16px; color: var(--color-secondary);">الألوان الأساسية للبراند (Core Brand Colors)</h3>
            <div class="grid-3">
                <div class="color-swatch-card" onclick="copyHex('#B52046', 'Mo7i Crimson')">
                    <div class="color-display" style="background-color: #B52046;">
                        #B52046
                    </div>
                    <div class="color-info">
                        <div class="color-name">Mo7i Crimson (الأساسي)</div>
                        <div class="color-hex"><span>primaryColor</span> <span>#B52046</span></div>
                        <div class="color-usage">الأزرار التفاعلية، الروابط النشطة، أيقونات التبويب المختارة.</div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#961837', 'Primary Hover')">
                    <div class="color-display" style="background-color: #961837;">
                        #961837
                    </div>
                    <div class="color-info">
                        <div class="color-name">Primary Hover (التفاعل)</div>
                        <div class="color-hex"><span>primaryHover</span> <span>#961837</span></div>
                        <div class="color-usage">حالة التحويم (Hover) فوق أزرار الإجراء وضغط الأزرار في الجوال.</div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#201A1A', 'Mo7i Charcoal')">
                    <div class="color-display" style="background-color: #201A1A;">
                        #201A1A
                    </div>
                    <div class="color-info">
                        <div class="color-name">Mo7i Charcoal (الثانوي)</div>
                        <div class="color-hex"><span>secondaryColor</span> <span>#201A1A</span></div>
                        <div class="color-usage">العناوين الرئيسية، الخلفيات الداكنة، الهيدر الفخم.</div>
                    </div>
                </div>
            </div>

            <h3 style="font-size: 18px; margin-bottom: 16px; color: var(--color-secondary);">ألوان حالات الطلبات والعمليات (Status & Functional Colors)</h3>
            <div class="grid-4">
                <div class="color-swatch-card" onclick="copyHex('#0DAE4A', 'Delivered')">
                    <div class="color-display" style="background-color: #0DAE4A;">#0DAE4A</div>
                    <div class="color-info">
                        <div class="color-name">تم التوصيل / Delivered</div>
                        <div class="color-hex"><span>successStatus</span> <span>#0DAE4A</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#E69914', 'Pending')">
                    <div class="color-display" style="background-color: #E69914;">#E69914</div>
                    <div class="color-info">
                        <div class="color-name">قيد الانتظار / Pending</div>
                        <div class="color-hex"><span>pendingStatus</span> <span>#E69914</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#C44D6B', 'Shipped')">
                    <div class="color-display" style="background-color: #C44D6B;">#C44D6B</div>
                    <div class="color-info">
                        <div class="color-name">خرج للشحن / Shipped</div>
                        <div class="color-hex"><span>shippedStatus</span> <span>#C44D6B</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#EE1C2F', 'Cancelled')">
                    <div class="color-display" style="background-color: #EE1C2F;">#EE1C2F</div>
                    <div class="color-info">
                        <div class="color-name">ملغي / Cancelled</div>
                        <div class="color-hex"><span>errorColor</span> <span>#EE1C2F</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Typography Specimen -->
        <section id="typography" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-font' style="color: var(--color-primary)"></i> 3. نظام التايبوغرافي ثنائي اللغة (Bilingual Typography)</h2>
            <p class="section-desc">خط <strong>Cairo</strong> للنصوص العربية و <strong>Rubik</strong> للنصوص الإنجليزية والأرقام والأسعار.</p>

            <div class="card">
                <div class="type-specimen">
                    <div>
                        <h1 style="font-size: 32px; font-weight: 800; color: var(--color-secondary);">عنوان البطل الرئيسي (H1 Display Hero)</h1>
                        <p style="font-family: var(--font-en); font-size: 20px; font-weight: 700; color: var(--color-primary);">Discover the Best Shopping Experience at Mo7i Store</p>
                    </div>
                    <div class="type-meta">32px / 2.0rem • Bold 800</div>
                </div>

                <div class="type-specimen">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 700; color: var(--color-secondary);">عناوين الأقسام والمنتجات (H2 Section Heading)</h2>
                        <p style="font-family: var(--font-en); font-size: 16px; font-weight: 600; color: var(--neutral-600);">Featured Products & Category Highlights</p>
                    </div>
                    <div class="type-meta">24px / 1.5rem • Bold 700</div>
                </div>

                <div class="type-specimen">
                    <div>
                        <p style="font-size: 16px; font-weight: 500; color: var(--neutral-800);">النصوص العادية وأوصاف المنتجات وقوائم المواصفات الفنية وسطور الفواتير والمعاملات اليومية.</p>
                        <p style="font-family: var(--font-en); font-size: 15px; font-weight: 400; color: var(--neutral-600);">Standard body text with high readability and optimized line heights.</p>
                    </div>
                    <div class="type-meta">16px / 1.0rem • Regular 400</div>
                </div>

                <div class="type-specimen">
                    <div>
                        <span style="font-family: var(--font-en); font-size: 22px; font-weight: 800; color: var(--color-primary);">$129.99</span>
                        <span style="font-size: 18px; font-weight: 700; color: var(--color-secondary); margin-right: 12px;">3,999 جنيه مصري</span>
                    </div>
                    <div class="type-meta">Price Token • Bold 800</div>
                </div>
            </div>
        </section>

        <!-- 4. UI Components Preview -->
        <section id="components" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-cube-alt' style="color: var(--color-primary)"></i> 4. نماذج المكونات التفاعلية (Component Tokens)</h2>
            <p class="section-desc">أزرار وحقول وشارات موحدة مطابقة لقواعد التصميم المعتمدة.</p>

            <div class="grid-2">
                <!-- Buttons & Badges -->
                <div class="card">
                    <h3 style="font-size: 18px; margin-bottom: 16px;">الأزرار والشارات (Buttons & Badges)</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
                        <button class="btn btn-primary"><i class='bx bx-shopping-bag'></i> أضف إلى السلة</button>
                        <button class="btn btn-secondary"><i class='bx bx-credit-card'></i> إتمام الشراء</button>
                        <button class="btn btn-outline"><i class='bx bx-heart'></i> المفضلة</button>
                    </div>

                    <h4 style="font-size: 15px; margin-bottom: 12px; color: var(--neutral-600);">شارات حالات الطلب (Status Pills)</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="badge-status" style="background: var(--color-success-light); color: var(--color-success);">
                            <i class='bx bx-check-circle'></i> تم التسليم
                        </span>
                        <span class="badge-status" style="background: var(--color-pending-light); color: var(--color-pending);">
                            <i class='bx bx-time'></i> قيد المراجعة
                        </span>
                        <span class="badge-status" style="background: #F4E8EC; color: var(--color-shipped);">
                            <i class='bx bx-package'></i> جاري التوصيل
                        </span>
                        <span class="badge-status" style="background: var(--color-error-light); color: var(--color-error);">
                            <i class='bx bx-x-circle'></i> ملغي
                        </span>
                    </div>
                </div>

                <!-- Form Controls -->
                <div class="card">
                    <h3 style="font-size: 18px; margin-bottom: 16px;">حقول الإدخال والبطاقات (Inputs & Cards)</h3>
                    <div style="margin-bottom: 16px;">
                        <label style="font-size: 14px; font-weight: 600; display: block; margin-bottom: 6px;">البريد الإلكتروني أو اسم المستخدم</label>
                        <input type="text" value="admin@mo7i-store.com" style="width: 100%; padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--neutral-300); font-family: var(--font-en); font-size: 15px; outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(181, 32, 70, 0.15);">
                    </div>
                    <div style="background: var(--color-primary-light); border: 1px solid rgba(181, 32, 70, 0.2); border-radius: var(--radius-md); padding: 16px;">
                        <div style="font-weight: 700; color: var(--color-primary); margin-bottom: 4px;"><i class='bx bx-bell'></i> إشعار الخصم الخاص</div>
                        <p style="font-size: 13px; color: var(--neutral-700);">شحن مجاني على كافة الطلبات التي تتجاوز 500 جنيه مصري باستخدام كوبون MO7I2026.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. AI Agent Rules & Tokens JSON -->
        <section id="agents" style="margin-bottom: 40px;">
            <h2 class="section-title"><i class='bx bx-bot' style="color: var(--color-primary)"></i> 5. قواعد الذكاء الاصطناعي (AI Agent Rulebook)</h2>
            <p class="section-desc">أي AI Agent في المستودع يلتزم بهذه القواعد بشكل قطعي لتجنب أي عشوائية في التصميم (Anti-Slop Guidelines).</p>

            <div class="code-container">
                <button class="copy-btn" onclick="copySnippet()">نسخ القواعد</button>
<pre id="agent-rules">
// ==============================================================================
// MO7I STORE - AI AGENT MANDATORY DESIGN SYSTEM INVARIANTS
// File: .agents/rules/design-system.md | tokens/design-tokens.json
// ==============================================================================

1. BRAND COLOR ENFORCEMENT:
   - Primary: #B52046 (Crimson) -> Flutter: primaryColor
   - Secondary: #201A1A (Charcoal) -> Flutter: secondaryColor
   - DO NOT invent arbitrary hex colors (e.g. #3b82f6 or #ec4899) for brand elements.

2. BILINGUAL & BIDIRECTIONAL LAYOUTS (RTL/LTR):
   - Always support Arabic first.
   - Use CSS logical properties: margin-inline-start, padding-inline-end.
   - In Flutter, use EdgeInsetsDirectional instead of EdgeInsets.only(left: ...).

3. TYPOGRAPHY MATRIX:
   - Arabic: 'Cairo' or 'Tajawal'
   - Latin / Numbers: 'Rubik' or 'Poppins'
   - Scale: H1 (32px), H2 (24px), H3 (20px), Body (16px), Small (14px), Caption (12px).

4. BORDER RADIUSES:
   - Buttons & Inputs: 8px (radius-md)
   - Cards & Dialogs: 12px (radius-lg)
   - Bottom Sheets: 16px (radius-xl)
   - Avatar / Badges: 9999px (radius-full)
</pre>
            </div>
        </section>

    </div>

    <!-- Toast Notification -->
    <div id="toast">تم النسخ بنجاح!</div>

    <script>
        function copyHex(hex, name) {
            navigator.clipboard.writeText(hex).then(() => {
                showToast(`تم نسخ لون ${name} (${hex}) إلى الحافظة!`);
            });
        }

        function copySnippet() {
            const code = document.getElementById('agent-rules').innerText;
            navigator.clipboard.writeText(code).then(() => {
                showToast("تم نسخ قواعد الـ AI Agent بنجاح!");
            });
        }

        function showToast(msg) {
            const t = document.getElementById('toast');
            t.innerText = msg;
            t.style.display = 'block';
            setTimeout(() => {
                t.style.display = 'none';
            }, 2500);
        }
    </script>
</body>
</html>
