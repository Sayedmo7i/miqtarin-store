<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MUQTARIN Store (مقترن ستور) - الهوية البصرية ونظام التصميم الموحد</title>
    
    <!-- Fonts: Tajawal for Arabic, Inter for English -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            /* Brand Colors - MUQTARIN Store Official Palette */
            --color-primary: #0F4C3A;
            --color-primary-hover: #0A3528;
            --color-primary-light: #E6F2ED;
            
            --color-emerald: #22C55E;
            --color-emerald-dark: #16A34A;
            --color-emerald-light: #E7F8EE;

            --color-gold: #F6C667;
            --color-gold-dark: #B45309;
            --color-gold-light: #FEF7E8;

            --color-secondary: #1F2937;
            --color-secondary-surface: #111827;

            /* Functional & Status Colors */
            --color-success: #16A34A;
            --color-success-light: #E7F8EE;
            --color-pending: #D97706;
            --color-pending-light: #FEF7E8;
            --color-received: #2563EB;
            --color-received-light: #EFF6FF;
            --color-processed: #7C3AED;
            --color-processed-light: #F5F3FF;
            --color-shipped: #0F766E;
            --color-shipped-light: #F0FDFA;
            --color-error: #DC2626;
            --color-error-light: #FDE8EA;
            --color-info: #0284C7;
            --color-info-light: #F0F9FF;

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
            --font-ar: 'Tajawal', sans-serif;
            --font-en: 'Inter', sans-serif;
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
            --shadow-brand: 0 8px 24px rgba(15, 76, 58, 0.25);
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
            border: 1px solid rgba(15, 76, 58, 0.2);
        }

        .badge-parent {
            background-color: #FEF7E8;
            color: var(--color-gold-dark);
            border: 1px solid rgba(246, 198, 103, 0.4);
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .badge-parent:hover {
            background-color: #F6C667;
            color: #1F2937;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Hero Banner */
        .hero {
            background: linear-gradient(135deg, #0F4C3A 0%, #1F2937 100%);
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
            background: radial-gradient(circle, rgba(34, 197, 94, 0.25) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-title {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 12px;
        }

        .hero-title span {
            color: var(--color-emerald);
        }

        .hero-subtitle {
            font-size: 18px;
            color: var(--neutral-300);
            max-width: 750px;
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
            height: 200px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid var(--neutral-200);
            padding: 24px;
        }

        .asset-preview.dark {
            background: #111827;
        }

        .asset-preview img {
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
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--color-secondary);
        }

        .asset-meta {
            font-family: var(--font-code);
            font-size: 12px;
            color: var(--color-primary);
            background: var(--neutral-100);
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
            direction: ltr;
        }

        /* Color Swatches */
        .color-swatch-card {
            background: #ffffff;
            border-radius: var(--radius-lg);
            border: 1px solid var(--neutral-200);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .color-swatch-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--color-emerald);
        }

        .color-display {
            height: 120px;
            display: flex;
            align-items: flex-end;
            padding: 12px;
            color: #ffffff;
            font-family: var(--font-code);
            font-weight: 600;
            font-size: 14px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .color-display.light {
            color: var(--neutral-900);
            text-shadow: none;
        }

        .color-info {
            padding: 16px;
        }

        .color-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
            color: var(--neutral-900);
        }

        .color-hex {
            display: flex;
            justify-content: space-between;
            font-family: var(--font-code);
            font-size: 13px;
            color: var(--neutral-500);
            direction: ltr;
        }

        .color-usage {
            font-size: 12px;
            color: var(--neutral-600);
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed var(--neutral-200);
        }

        /* Typography specimens */
        .type-specimen {
            border-bottom: 1px solid var(--neutral-200);
            padding: 24px 0;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 20px;
        }

        .type-specimen:last-child {
            border-bottom: none;
        }

        .type-meta {
            font-family: var(--font-code);
            color: var(--neutral-500);
            font-size: 13px;
            direction: ltr;
            white-space: nowrap;
        }

        /* Buttons & UI controls */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-family: var(--font-ar);
            font-size: 15px;
            font-weight: 700;
            border-radius: var(--radius-md);
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--color-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 76, 58, 0.24);
        }

        .btn-primary:hover {
            background-color: var(--color-primary-hover);
        }

        .btn-secondary {
            background-color: var(--color-secondary);
            color: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #111827;
        }

        .btn-emerald {
            background-color: var(--color-emerald);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.24);
        }

        .btn-emerald:hover {
            background-color: var(--color-emerald-dark);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
        }

        .btn-outline:hover {
            background-color: var(--color-primary-light);
        }

        /* Code display */
        .code-container {
            background: #111827;
            color: #e2e8f0;
            padding: 24px;
            border-radius: var(--radius-lg);
            font-family: var(--font-code);
            font-size: 14px;
            line-height: 1.7;
            overflow-x: auto;
            direction: ltr;
            text-align: left;
            position: relative;
        }

        .copy-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            cursor: pointer;
            font-family: var(--font-ar);
        }

        .copy-btn:hover {
            background: rgba(255,255,255,0.2);
        }

        /* Toast */
        #toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            background: #1F2937;
            color: #ffffff;
            padding: 12px 24px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="{{ url('/') }}" class="brand-logo-wrap">
                <img src="{{ asset('assets/brand/logo.svg') }}" alt="MUQTARIN Store Logo" class="brand-logo-img">
                <span class="badge-status badge-brand"><i class='bx bx-check-shield'></i> MUQTARIN Store Design System</span>
            </a>
            <div class="header-actions">
                <a href="https://app.vendoratravel.com/" target="_blank" class="badge-status badge-parent">
                    <i class='bx bx-link-external'></i> دليل الشركة الأم (مُـقْـتَـرَن)
                </a>
                <a href="{{ route('admin.login') }}" class="btn btn-outline" style="padding: 6px 16px; font-size: 13px;">لوحة الإدارة</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <h1 class="hero-title">دليل الهوية البصرية ونظام التصميم لـ <span>MUQTARIN Store</span></h1>
        <p class="hero-subtitle">
            نربط الفكرة بالتنفيذ • Connecting Ideas to Execution | الدليل الشامل والمرجع الرسمي (Single Source of Truth) لكافة المطورين وروبوتات الذكاء الاصطناعي (AI Agents) لتوحيد التصميم عبر الويب، لوحة الإدارة، وتطبيقات الجوال.
        </p>
        <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
            <a href="#logos" class="btn btn-primary"><i class='bx bx-images'></i> استعراض الشعارات</a>
            <a href="#colors" class="btn btn-emerald"><i class='bx bx-palette'></i> لوحة الألوان</a>
            <a href="#agents" class="btn btn-secondary"><i class='bx bx-bot'></i> قواعد الـ AI Agents</a>
        </div>
    </section>

    <!-- Main Content Container -->
    <div class="container">

        <!-- 1. Brand Logos & Assets -->
        <section id="logos" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-crown' style="color: var(--color-primary)"></i> 1. الشعارات الرسمية والأيقونات (Brand Assets)</h2>
            <p class="section-desc">الأصول المعتمدة لهوية مقترن ستور — الرمز المتداخل الرسمي المأخوذ من الدليل التفاعلي للشركة الأم، مع الشعار الكامل للمتجر بالوضعين الفاتح والداكن.</p>

            <div class="grid-3">
                <!-- Vector Master Logo (Light) -->
                <div class="asset-card">
                    <div class="asset-preview">
                        <img src="{{ asset('assets/brand/logo.svg') }}" alt="MUQTARIN Store Light Logo">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">الشعار الرئيسي للخلفيات الفاتحة (Light Lockup)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">يستخدم في الهيدر الفاتح، الفواتير، المطبوعات، وصفحات تسجيل الدخول الفاتحة.</p>
                        <div class="asset-meta">/assets/brand/logo.svg</div>
                    </div>
                </div>

                <!-- Vector Master Logo (Dark) -->
                <div class="asset-card">
                    <div class="asset-preview dark">
                        <img src="{{ asset('assets/brand/logo-dark.svg') }}" alt="MUQTARIN Store Dark Logo">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">الشعار الرئيسي للخلفيات الداكنة (Dark Lockup)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">تباين عالي وفخامة معمارية مخصصة للهيدر الداكن، القوائم الجانبية، وشاشات الوضع الليلي.</p>
                        <div class="asset-meta">/assets/brand/logo-dark.svg</div>
                    </div>
                </div>

                <!-- Interlocking App Icon Symbol -->
                <div class="asset-card">
                    <div class="asset-preview">
                        <img src="{{ asset('assets/brand/icon.svg') }}" alt="MUQTARIN Ribbon Symbol" style="width: 120px; height: 120px;">
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">رمز الاقتران الرسمي (Interlocking Ribbon Mark)</div>
                        <p style="color: var(--neutral-600); font-size: 14px;">الرمز المتداخل الثنائي (Pine + Emerald) المعتمد في تطبيقات الجوال والـ Favicon.</p>
                        <div class="asset-meta">/assets/brand/icon.svg</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Color Palette & Tokens -->
        <section id="colors" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-palette' style="color: var(--color-primary)"></i> 2. لوحة الألوان الرسمية (Color Palette)</h2>
            <p class="section-desc">اضغط على أي كارت لنسخ رمز اللون الست عشري (Hex Code) أو اسم متغير Flutter مباشرة.</p>

            <h3 style="font-size: 18px; margin-bottom: 16px; color: var(--color-secondary);">الألوان الأساسية للعلامة (Core Brand Colors)</h3>
            <div class="grid-4">
                <div class="color-swatch-card" onclick="copyHex('#0F4C3A', 'MUQTARIN Pine')">
                    <div class="color-display" style="background-color: #0F4C3A;">
                        #0F4C3A
                    </div>
                    <div class="color-info">
                        <div class="color-name">MUQTARIN Pine (الأساسي)</div>
                        <div class="color-hex"><span>primaryColor</span> <span>#0F4C3A</span></div>
                        <div class="color-usage">الأزرار التفاعلية الرئيسية، الهيدر الرسمي، الحالة النشطة.</div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#22C55E', 'MUQTARIN Emerald')">
                    <div class="color-display" style="background-color: #22C55E;">
                        #22C55E
                    </div>
                    <div class="color-info">
                        <div class="color-name">MUQTARIN Emerald (الحيوي)</div>
                        <div class="color-hex"><span>emeraldColor</span> <span>#22C55E</span></div>
                        <div class="color-usage">حلقة الرمز الثانية، شارات النجاح، تمييز كلمة Store.</div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#F6C667', 'MUQTARIN Gold')">
                    <div class="color-display light" style="background-color: #F6C667;">
                        #F6C667
                    </div>
                    <div class="color-info">
                        <div class="color-name">MUQTARIN Gold (المتجر)</div>
                        <div class="color-hex"><span>goldColor</span> <span>#F6C667</span></div>
                        <div class="color-usage">شارات العروض، التقييمات، إبراز القيمة الاستثنائية للمتجر.</div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#1F2937', 'MUQTARIN Ink')">
                    <div class="color-display" style="background-color: #1F2937;">
                        #1F2937
                    </div>
                    <div class="color-info">
                        <div class="color-name">MUQTARIN Ink (الثانوي)</div>
                        <div class="color-hex"><span>secondaryColor</span> <span>#1F2937</span></div>
                        <div class="color-usage">النصوص التحريرية، العناوين ذات المقروئية العالية.</div>
                    </div>
                </div>
            </div>

            <h3 style="font-size: 18px; margin-bottom: 16px; color: var(--color-secondary);">ألوان حالات الطلبات والعمليات (Status & Functional Colors)</h3>
            <div class="grid-4">
                <div class="color-swatch-card" onclick="copyHex('#16A34A', 'Delivered')">
                    <div class="color-display" style="background-color: #16A34A;">#16A34A</div>
                    <div class="color-info">
                        <div class="color-name">تم التوصيل / Delivered</div>
                        <div class="color-hex"><span>successStatus</span> <span>#16A34A</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#D97706', 'Pending')">
                    <div class="color-display" style="background-color: #D97706;">#D97706</div>
                    <div class="color-info">
                        <div class="color-name">قيد الانتظار / Pending</div>
                        <div class="color-hex"><span>pendingStatus</span> <span>#D97706</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#0F766E', 'Shipped')">
                    <div class="color-display" style="background-color: #0F766E;">#0F766E</div>
                    <div class="color-info">
                        <div class="color-name">خرج للشحن / Shipped</div>
                        <div class="color-hex"><span>shippedStatus</span> <span>#0F766E</span></div>
                    </div>
                </div>

                <div class="color-swatch-card" onclick="copyHex('#DC2626', 'Cancelled')">
                    <div class="color-display" style="background-color: #DC2626;">#DC2626</div>
                    <div class="color-info">
                        <div class="color-name">ملغي / Cancelled</div>
                        <div class="color-hex"><span>errorColor</span> <span>#DC2626</span></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Typography Specimen -->
        <section id="typography" style="margin-bottom: 60px;">
            <h2 class="section-title"><i class='bx bx-font' style="color: var(--color-primary)"></i> 3. نظام التايبوغرافي ثنائي اللغة (Bilingual Typography)</h2>
            <p class="section-desc">خط <strong>Tajawal</strong> للنصوص العربية و <strong>Inter</strong> للنصوص الإنجليزية والأرقام والأسعار.</p>

            <div class="card">
                <div class="type-specimen">
                    <div>
                        <h1 style="font-size: 32px; font-weight: 800; color: var(--color-secondary);">عنوان البطل الرئيسي (H1 Display Hero)</h1>
                        <p style="font-family: var(--font-en); font-size: 20px; font-weight: 700; color: var(--color-primary);">Discover the Best Shopping Experience at MUQTARIN Store</p>
                    </div>
                    <div class="type-meta">32px / 2.0rem • Bold 800</div>
                </div>

                <div class="type-specimen">
                    <div>
                        <h2 style="font-size: 24px; font-weight: 700; color: var(--color-secondary);">عناوين الأقسام والمنتجات (H2 Section Heading)</h2>
                        <p style="font-family: var(--font-en); font-size: 16px; font-weight: 600; color: var(--neutral-600);">Connecting Ideas to Execution in Unified Commerce</p>
                    </div>
                    <div class="type-meta">24px / 1.5rem • Bold 700</div>
                </div>

                <div class="type-specimen">
                    <div>
                        <p style="font-size: 16px; font-weight: 500; color: var(--neutral-800);">بناء هندسي متناسق ووضوح فائق في القراءة الرقمية، مع الحفاظ على الرصانة والأناقة التحريرية للنصوص العربية.</p>
                        <p style="font-family: var(--font-en); font-size: 15px; font-weight: 400; color: var(--neutral-600);">Supreme geometric clarity, high legibility across small displays, and balanced proportion.</p>
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
                        <button class="btn btn-emerald"><i class='bx bx-check'></i> زر الزمرد</button>
                        <button class="btn btn-secondary"><i class='bx bx-credit-card'></i> إتمام الشراء</button>
                        <button class="btn btn-outline"><i class='bx bx-heart'></i> المفضلة</button>
                    </div>

                    <h4 style="font-size: 15px; margin-bottom: 12px; color: var(--neutral-600);">شارات حالات الطلب (Status Pills)</h4>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <span class="badge-status" style="background: var(--color-success-light); color: var(--color-success);">
                            <i class='bx bx-check-circle'></i> تم التسليم
                        </span>
                        <span class="badge-status" style="background: var(--color-pending-light); color: var(--color-pending);">
                            <i class='bx bx-time'></i> قيد الانتظار
                        </span>
                        <span class="badge-status" style="background: var(--color-shipped-light); color: var(--color-shipped);">
                            <i class='bx bx-package'></i> خرج للشحن
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
                        <input type="text" value="admin@muqtarin-store.com" style="width: 100%; padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--neutral-300); font-family: var(--font-en); font-size: 15px; outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px rgba(15, 76, 58, 0.15);">
                    </div>
                    <div style="background: var(--color-primary-light); border: 1px solid rgba(15, 76, 58, 0.2); border-radius: var(--radius-md); padding: 16px;">
                        <div style="font-weight: 700; color: var(--color-primary); margin-bottom: 4px;"><i class='bx bx-bell'></i> إشعار العرض المميز</div>
                        <p style="font-size: 13px; color: var(--neutral-700);">شحن مجاني على كافة الطلبات التي تتجاوز 500 جنيه مصري في مقترن ستور.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. AI Agent Rules -->
        <section id="agents" style="margin-bottom: 40px;">
            <h2 class="section-title"><i class='bx bx-bot' style="color: var(--color-primary)"></i> 5. قواعد الذكاء الاصطناعي (AI Agent Rulebook)</h2>
            <p class="section-desc">أي AI Agent في المستودع يلتزم بهذه القواعد بشكل قطعي لتجنب أي عشوائية في التصميم (Anti-Slop Guidelines).</p>

            <div class="code-container">
                <button class="copy-btn" onclick="copySnippet()">نسخ القواعد</button>
<pre id="agent-rules">
// ==============================================================================
// MUQTARIN STORE (مقترن ستور) - AI AGENT MANDATORY DESIGN SYSTEM INVARIANTS
// File: .agents/rules/design-system.md | Specification: /DESIGN.md
// ==============================================================================

1. BRAND COLOR ENFORCEMENT:
   - Primary: #0F4C3A (Pine) -> Flutter: primaryColor
   - Accent: #22C55E (Emerald) / Text-safe on light: #16A34A -> Flutter: emeraldColor
   - Store Accent: #F6C667 (Gold) -> Flutter: goldColor
   - Secondary / Ink: #1F2937 -> Flutter: secondaryColor
   - DO NOT invent arbitrary hex colors. Old crimson #B52046 is retired.

2. BILINGUAL & BIDIRECTIONAL LAYOUTS (RTL/LTR):
   - Always support Arabic first.
   - Use CSS logical properties: margin-inline-start, padding-inline-end.
   - In Flutter, use EdgeInsetsDirectional instead of EdgeInsets.only(left: ...).

3. TYPOGRAPHY MATRIX:
   - Arabic: 'Tajawal' (fallback 'Cairo')
   - Latin / English: 'Inter' ('Space Grotesk' for display / wordmark only)
   - Old fonts Rubik / Poppins are retired.

4. BORDER RADIUSES & ELEVATION:
   - Buttons & Inputs: 8px (radius-md)
   - Cards & Dialogs: 12px (radius-lg)
   - Brand Glow: 0 6px 18px rgba(15, 76, 58, 0.24)

5. NO DESIGN-TOKENS.JSON:
   - design-tokens.json is forbidden in this project by decision of the owner.
   - Tokens live in DESIGN.md, .agents/rules/design-system.md, and code variables.
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
