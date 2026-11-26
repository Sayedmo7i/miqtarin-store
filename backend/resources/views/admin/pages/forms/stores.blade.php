@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.add_store', 'Add Store') }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.add_store', 'Add Store')" :subtitle="labels(
        'admin_labels.lets_unlesh_a_wave_of_new_stores_on_your_marketplace',
        'Let’s unleash a wave of new stores on your marketplace.',
    )" :breadcrumbs="[
        ['label' => labels('admin_labels.stores', 'Stores')],
        ['label' => labels('admin_labels.add_store', 'Add Store')],
    ]" />
    @php
        use App\Services\MediaService;
        use App\Services\SettingService;
        $shipping_settings = app(SettingService::class)->getSettings('shipping_method', true);
        $shipping_settings = json_decode($shipping_settings, true);
        $shiprocketEnabled =
            isset($shipping_settings['shiprocket_shipping_method']) &&
            $shipping_settings['shiprocket_shipping_method'] == 1;
    @endphp

    <div class="col-xxl-12 p-0 muqtarin-store-wizard">
        <div class="row g-4 align-items-start">
            {{-- Left Vertical Timeline Stepper Rail (OriginUI / 21st.dev) --}}
            <div class="col-md-12 col-xl-3 col-xxl-3">
                <div class="card muqtarin-wizard-rail">
                    <div class="muqtarin-wizard-rail-head">
                        <div class="muqtarin-wizard-progress-meta">
                            <span class="muqtarin-wizard-step-pill" id="muqtarin-wizard-step-counter">1 / 8</span>
                            <span class="muqtarin-wizard-progress-pct" id="muqtarin-wizard-pct">13%</span>
                        </div>
                        <div class="muqtarin-wizard-progress-track">
                            <div class="muqtarin-wizard-progress-fill" id="muqtarin-wizard-progress-fill"></div>
                        </div>
                    </div>

                    <div class="card1">
                        <ul id="store_progressbar">
                            <li class="active step0" data-state="current">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">1</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.store_details', 'Store Details') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">2</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.store_images', 'Store Images') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">3</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.app_settings', 'App & Web Setting') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">4</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.cards_display_styles', 'Cards Display Styles') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">5</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.categories_display_style', 'Categories Display Style') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">6</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.brands_display_style', 'Brands And Wishlist Display Style') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">7</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.offer_display_style', 'Offer Display Style') }}</span>
                            </li>
                            <li class="step0" data-state="upcoming">
                                <span class="muqtarin-step-dot">
                                    <span class="muqtarin-step-num">8</span>
                                    <svg class="muqtarin-step-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                </span>
                                <span class="muqtarin-step-text">{{ labels('admin_labels.delivery_charge_setting', 'Delivery Charge Setting') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Right Step Panels --}}
            <div class="col-md-12 col-xl-9 muqtarin-form-column">
                <form id="" action="{{ route('admin.stores.store') }}" class="submit_form"
                    enctype="multipart/form-data" method="POST">
                    @csrf

                    {{-- STEP 1: Store Details --}}
                    <div class="card2 first-screen show">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-2 2a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12a2 2 0 0 1-2-2V7"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.store_details', 'Store Details') }}</h5>
                                    </div>

                                    <ul class="nav nav-tabs muqtarin-segmented-tabs" id="brandTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="language-nav-link nav-link active" id="tab-en"
                                                data-bs-toggle="tab" data-bs-target="#content-en" type="button"
                                                role="tab" aria-controls="content-en" aria-selected="true">
                                                {{ labels('admin_labels.default', 'Default') }}
                                            </button>
                                        </li>
                                        <x-language.multi_language_tabs :languages="$languages" />
                                    </ul>

                                    <div class="tab-content mt-3" id="brandTabsContent">
                                        <div class="tab-pane fade show active" id="content-en" role="tabpanel"
                                            aria-labelledby="tab-en">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="title" class="form-label">
                                                        {{ labels('admin_labels.name', 'Name') }}
                                                        <span class="text-asterisks text-sm">*</span>
                                                    </label>
                                                    <input type="text"
                                                        placeholder="{{ labels('admin_labels.grocery_placeholder', 'Grocery') }}"
                                                        name="name" class="form-control" value="{{ old('name') }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="description" class="form-label">
                                                        {{ labels('admin_labels.description', 'Description') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="description"
                                                        id="description" value=""
                                                        placeholder="{{ labels('admin_labels.description_placeholder', 'Description') }}">
                                                </div>
                                            </div>
                                        </div>

                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <div class="tab-pane fade" id="content-{{ $lang->code }}" role="tabpanel"
                                                    aria-labelledby="tab-{{ $lang->code }}">
                                                    <div class="row g-3">
                                                        <div class="col-md-12">
                                                            <label for="translated_title_{{ $lang->code }}"
                                                                class="form-label">
                                                                {{ labels('admin_labels.name', 'Name') }}
                                                                ({{ $lang->language }})
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="translated_title_{{ $lang->code }}"
                                                                name="translated_store_name[{{ $lang->code }}]"
                                                                value="">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="translated_short_description_{{ $lang->code }}"
                                                                class="form-label">
                                                                {{ labels('admin_labels.description', 'Description') }}
                                                                ({{ $lang->language }})
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                id="translated_short_description_{{ $lang->code }}"
                                                                name="translated_store_description[{{ $lang->code }}]"
                                                                value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <div class="muqtarin-switch-card">
                                                <label for="is_default_store">
                                                    {{ labels('admin_labels.is_default_store', 'Is Default Store') }}?
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_default_store"
                                                        name="is_default_store">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="muqtarin-switch-card">
                                                <label for="is_single_seller_order_system">
                                                    {{ labels('admin_labels.single_seller_order_system', 'Single Seller Order System') }}?
                                                    <small class="text-muted">({{ labels('admin_labels.for_cart', 'For Cart') }})</small>
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="is_single_seller_order_system"
                                                        name="is_single_seller_order_system">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <label for="note_for_necessary_documents" class="form-label">
                                            {{ labels('admin_labels.note_for_necessary_documents', 'Note for Necessary Documents') }}
                                        </label>
                                        <textarea id="note_for_necessary_documents" name="note_for_necessary_documents" class="form-control" rows="3"
                                            placeholder="{{ labels('admin_labels.place_food_license_in_other_documents', 'Please attach the food license/pharmacy license in the other documents') }}"></textarea>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="light_theme_color_code">
                                                    {{ labels('admin_labels.primary_theme_color', 'Primary Theme Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color" value="#e0ffee" id="light_theme_color"
                                                        oninput="updateColorCode('light_theme_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="light_theme_color_code" name="primary_color"
                                                        class="form-control muqtarin-color-hex"
                                                        oninput="updateColorPicker('light_theme_color', this.value)">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="dark_theme_color_code">
                                                    {{ labels('admin_labels.secondary_theme_color', 'Secondary Theme Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color" value="#e0ffee" id="dark_theme_color"
                                                        oninput="updateColorCode('dark_theme_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="dark_theme_color_code"
                                                        oninput="updateColorPicker('dark_theme_color', this.value)"
                                                        name="secondary_color" class="form-control muqtarin-color-hex">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="hover_color_code">
                                                    {{ labels('admin_labels.link_hover_color', 'Link Hover Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color" value="#e0ffee" id="hover_color"
                                                        oninput="updateColorCode('hover_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="hover_color_code"
                                                        oninput="updateColorPicker('hover_color', this.value)"
                                                        name="hover_color" class="form-control muqtarin-color-hex">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="active_color_code">
                                                    {{ labels('admin_labels.link_active_color', 'Link Active Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color" value="#e0ffee" id="active_color"
                                                        oninput="updateColorCode('active_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="active_color_code"
                                                        oninput="updateColorPicker('active_color', this.value)"
                                                        name="active_color" class="form-control muqtarin-color-hex">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="background_color_code">
                                                    {{ labels('admin_labels.background_color', 'Background Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color" value="#e0ffee" id="background_color"
                                                        oninput="updateColorCode('background_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="background_color_code"
                                                        oninput="updateColorPicker('background_color', this.value)"
                                                        name="background_color" class="form-control muqtarin-color-hex">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step1">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: Store Images --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.store_images', 'Store Images') }}</h5>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="image">{{ labels('admin_labels.image', 'Image') }}
                                                    <span class='text-asterisks text-sm'>*</span>
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond" name="image"
                                                        data-max-file-size="30MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="banner_image" class="form-label">
                                                    {{ labels('admin_labels.banner_image', 'Banner Image') }}
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond" name="banner_image"
                                                        data-max-file-size="300MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step2">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 3: App & Web Setting --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.web_home_page_theme', 'Web Home Page Theme') }}</h5>
                                    </div>

                                    <div class="mb-3">
                                        <select class="web_home_page_theme form-control form-select"
                                            name="web_home_page_theme">
                                            <option value="web_home_page_theme_1">{{ labels('admin_labels.default_theme', 'Default') }}</option>
                                            <option value="web_home_page_theme_2">{{ labels('admin_labels.theme_2', 'Theme 2') }}</option>
                                            <option value="web_home_page_theme_3">{{ labels('admin_labels.theme_3', 'Theme 3') }}</option>
                                            <option value="web_home_page_theme_4">{{ labels('admin_labels.theme_4', 'Theme 4') }}</option>
                                            <option value="web_home_page_theme_5">{{ labels('admin_labels.theme_5', 'Theme 5') }}</option>
                                            <option value="web_home_page_theme_6">{{ labels('admin_labels.theme_6', 'Theme 6') }}</option>
                                        </select>
                                    </div>

                                    <div class="web_home_page_theme_images store_style_box home_theme_style_box">
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_1.png') }}"
                                            class="web_home_page_theme_1 home_theme home_theme_1" alt="Theme 1" />
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_2.png') }}"
                                            class="web_home_page_theme_2 home_theme home_theme_2" alt="{{ labels('admin_labels.theme_2', 'Theme 2') }}" />
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_3.png') }}"
                                            class="web_home_page_theme_3 home_theme home_theme_3" alt="{{ labels('admin_labels.theme_3', 'Theme 3') }}" />
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_4.png') }}"
                                            class="web_home_page_theme_4 home_theme home_theme_4" alt="{{ labels('admin_labels.theme_4', 'Theme 4') }}" />
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_5.png') }}"
                                            class="web_home_page_theme_5 home_theme home_theme_5" alt="{{ labels('admin_labels.theme_5', 'Theme 5') }}" />
                                        <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_6.png') }}"
                                            class="web_home_page_theme_6 home_theme home_theme_6" alt="{{ labels('admin_labels.theme_5', 'Theme 5') }}" />
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.app_images', 'App Images') }}</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="image">
                                                    {{ labels('admin_labels.banner_image_for_most_selling_products', 'Banner Image (For Most Selling Products (390 x 500))') }}
                                                    <span class='text-asterisks text-sm'>*</span>
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond"
                                                        name="banner_image_for_most_selling_product"
                                                        data-max-file-size="300MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="" class="form-label">
                                                    {{ labels('admin_labels.stack_image', 'Stack Image') }}(App Home Page Background Image)
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond" name="stack_image"
                                                        data-max-file-size="300MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="image">
                                                    {{ labels('admin_labels.login_page_image', 'Login Page Image (App Login Page Background Image (390 x 501))') }}
                                                    <span class='text-asterisks text-sm'>*</span>
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond" name="login_image"
                                                        data-max-file-size="300MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="muqtarin-upload-tile">
                                                <label for="image">
                                                    {{ labels('admin_labels.half_store_logo', 'Half Store Logo (try to upload square image (100 x 100))') }}
                                                    <span class='text-asterisks text-sm'>*</span>
                                                </label>
                                                <div class="text-center form-group mb-0">
                                                    <input type="file" class="filepond" name="half_store_logo"
                                                        data-max-file-size="300MB" data-max-files="20"
                                                        accept="image/*,.webp" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step3">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 4: Cards Display Styles --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.cards_display_style', 'Cards Display Style For App') }}</h5>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="store_display_style">
                                                    {{ labels('admin_labels.store_display_style', 'Stores display style') }}
                                                </label>
                                                <select class="feature_section_header_style form-control form-select"
                                                    name="store_style">
                                                    <option value="header_style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="header_style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="header_style_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="feature_section_header_style_images store_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_1.png') }}"
                                                    class="header_style_1" alt="{{ labels('admin_labels.style_1', 'Style 1') }}" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_2.png') }}"
                                                    class="header_style_2" alt="{{ labels('admin_labels.style_2', 'Style 2') }}" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_3.png') }}"
                                                    class="header_style_3" alt="{{ labels('admin_labels.style_3', 'Style 3') }}" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="">
                                                    {{ labels('admin_labels.product_display_style', 'Products display style') }}
                                                </label>
                                                <select class="product_card_style form-control form-select"
                                                    name="product_style">
                                                    <option value="style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="style_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="product_card_style_images product_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_1.png') }}"
                                                    class="style_1" alt="Product Card Style 1" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_2.png') }}"
                                                    class="style_2" alt="Product Card Style 2" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_3.png') }}"
                                                    class="style_3" alt="Product Card Style 3" />
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.cards_display_style', 'Cards Display Style For Web') }}</h5>
                                    <div class="mb-2">
                                        <label class="form-label" for="products_display_style_for_web">
                                            {{ labels('admin_labels.products_display_style_for_web', 'Products display style') }}
                                        </label>
                                        <select class="products_display_style_for_web form-control form-select"
                                            name="products_display_style_for_web">
                                            <option value="products_display_style_for_web_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                            <option value="products_display_style_for_web_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                            <option value="products_display_style_for_web_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                            <option value="products_display_style_for_web_4">{{ labels('admin_labels.style_4', 'Style 4') }}</option>
                                            <option value="products_display_style_for_web_5">{{ labels('admin_labels.style_5', 'Style 5') }}</option>
                                        </select>
                                        <div class="muqtarin-preview-frame">
                                            <div class="muqtarin-preview-frame-bar">
                                                <span class="muqtarin-preview-dots"><span></span><span></span><span></span></span>
                                                <span class="muqtarin-preview-badge">Web Preview</span>
                                            </div>
                                            <iframe id="products_display_style_for_web_iframe"
                                                src="/admin/web_product_card_style"></iframe>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.products_details_style', 'Products Details Style For Web') }}</h5>
                                    <div>
                                        <label class="form-label" for="web_product_details_style">
                                            {{ labels('admin_labels.products_details_style_for_web', 'Products details display style') }}
                                        </label>
                                        <select class="web_product_details_style form-control form-select mb-3"
                                            name="web_product_details_style">
                                            <option value="web_product_details_style_1">
                                                {{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                            <option value="web_product_details_style_2">
                                                {{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                        </select>
                                        <div class="web_product_details_style_images store_style_box home_theme_style_box">
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/details_1.png') }}"
                                                class="web_product_details_style_1" alt="Theme 1" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/details_2.png') }}"
                                                class="web_product_details_style_2" alt="{{ labels('admin_labels.theme_2', 'Theme 2') }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step4">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 5: Categories Display Style --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M7 12h10"/><path d="M10 18h4"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.categories_display_style', 'Categories Display Style') }}</h5>
                                    </div>

                                    <ul class="nav nav-tabs muqtarin-segmented-tabs" id="categoriesTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="language-nav-link nav-link active" id="tab-en"
                                                data-bs-toggle="tab" data-bs-target="#categorycontent-en"
                                                type="button" role="tab" aria-controls="content-en"
                                                aria-selected="true">
                                                {{ labels('admin_labels.default', 'Default') }}
                                            </button>
                                        </li>
                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <li class="nav-item" role="presentation">
                                                    <button class="language-nav-link nav-link"
                                                        id="tab-{{ $lang->code }}" data-bs-toggle="tab"
                                                        data-bs-target="#categorycontent-{{ $lang->code }}"
                                                        type="button" role="tab"
                                                        aria-controls="content-{{ $lang->code }}"
                                                        aria-selected="false">
                                                        {{ $lang->language }}
                                                    </button>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    <div class="tab-content mt-3" id="UpdatecategoriesTabsContent">
                                        <div class="tab-pane fade show active" id="categorycontent-en"
                                            role="tabpanel" aria-labelledby="tab-en">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="basic-default-fullname">{{ labels('admin_labels.categories_section_title', 'Categories Section Title') }}<span
                                                        class='text-asterisks text-sm'>*</span></label>
                                                <input type="text" class="form-control"
                                                    id="basic-default-fullname"
                                                    placeholder="{{ labels('admin_labels.shop_by_categories_placeholder', 'Shop By Categories') }}"
                                                    name="category_section_title">
                                            </div>
                                        </div>

                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <div class="tab-pane fade"
                                                    id="categorycontent-{{ $lang->code }}" role="tabpanel"
                                                    aria-labelledby="tab-{{ $lang->code }}">
                                                    <div class="mb-3">
                                                        <label
                                                            for="translated_categories_section_title_{{ $lang->code }}"
                                                            class="form-label">
                                                            {{ labels('admin_labels.categories_section_title', 'Categories Section Title') }}
                                                            ({{ $lang->language }})
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="translated_categories_section_title[{{ $lang->code }}]">
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="row g-4 mt-1">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="store_display_style">
                                                    {{ labels('admin_labels.categories_display_style', 'Categories Display Style') }}
                                                </label>
                                                <select class="categories_style form-control form-select"
                                                    name="category_style">
                                                    <option value="category_style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="category_style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                </select>
                                            </div>
                                            <div class="categories_style_images category_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_style_1.png') }}"
                                                    class="category_style_1" alt="Category Style 1" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_style_2.png') }}"
                                                    class="category_style_2" alt="Category Style 2" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="">
                                                    {{ labels('admin_labels.categories_card_style', 'Categories Cards Style') }}
                                                </label>
                                                <select class="categories_card_style form-control form-select"
                                                    name="category_card_style">
                                                    <option value="category_card_style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="category_card_style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="category_card_style_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="categories_card_style_images category_card_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_1.jpg') }}"
                                                    class="category_card_style_1" alt="Category Card Style 1" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_2.jpg') }}"
                                                    class="category_card_style_2" alt="Category Card Style 2" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_3.jpg') }}"
                                                    class="category_card_style_3" alt="Category Card Style 3" />
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.cards_display_style', 'Cards Display Style For Web') }}</h5>
                                    <div>
                                        <label class="form-label" for="categories_display_style_for_web">
                                            {{ labels('admin_labels.categories_display_style', 'Categories Display Style') }}
                                        </label>
                                        <select class="categories_display_style_for_web form-control form-select"
                                            name="categories_display_style_for_web">
                                            <option value="categories_display_style_for_web_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                            <option value="categories_display_style_for_web_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                            <option value="categories_display_style_for_web_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                        </select>
                                        <div class="muqtarin-preview-frame">
                                            <div class="muqtarin-preview-frame-bar">
                                                <span class="muqtarin-preview-dots"><span></span><span></span><span></span></span>
                                                <span class="muqtarin-preview-badge">Web Preview</span>
                                            </div>
                                            <iframe class="overflow-hidden"
                                                id="categories_display_style_for_web_iframe"
                                                src="/admin/web_categories_style"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step5">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 6: Brands & Wishlist Display Style --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" x2="7.01" y1="7" y2="7"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.brands_display_style', 'Brands Display Style') }}</h5>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="store_display_style">
                                                    {{ labels('admin_labels.brand_style', 'Brand Style') }}
                                                </label>
                                                <select class="brands_style form-control form-select"
                                                    name="brand_style">
                                                    <option value="brands_style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="brands_style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                </select>
                                            </div>
                                            <div class="brands_style_images category_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/brands_style_1.png') }}"
                                                    class="brands_style_1" alt="Brand Style 1" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/brands_style_2.png') }}"
                                                    class="brands_style_2" alt="Brand Style 2" />
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.brands_display_style', 'Brands & Wishlist Display Style For Web') }}</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label" for="brands_display_style">
                                                {{ labels('admin_labels.brands_display_style', 'Brands Display Style') }}
                                            </label>
                                            <select class="brands_display_style_for_web form-control form-select"
                                                name="brands_display_style_for_web">
                                                <option value="brands_display_style_for_web_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                <option value="brands_display_style_for_web_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                            </select>
                                            <div class="muqtarin-preview-frame">
                                                <div class="muqtarin-preview-frame-bar">
                                                    <span class="muqtarin-preview-dots"><span></span><span></span><span></span></span>
                                                    <span class="muqtarin-preview-badge">Web Preview</span>
                                                </div>
                                                <iframe class="overflow-hidden"
                                                    id="brands_display_style_for_web_iframe"
                                                    src="/admin/web_brands_style"></iframe>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label" for="brands_display_style">
                                                {{ labels('admin_labels.wishlist_display_style', 'Wishlist Display Style') }}
                                            </label>
                                            <select class="wishlist_display_style_for_web form-control form-select"
                                                name="wishlist_display_style_for_web">
                                                <option value="wishlist_display_style_for_web_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                <option value="wishlist_display_style_for_web_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                            </select>
                                            <div class="muqtarin-preview-frame">
                                                <div class="muqtarin-preview-frame-bar">
                                                    <span class="muqtarin-preview-dots"><span></span><span></span><span></span></span>
                                                    <span class="muqtarin-preview-badge">Web Preview</span>
                                                </div>
                                                <iframe class="overflow-hidden"
                                                    id="wishlist_display_style_for_web_iframe"
                                                    src="/admin/web_wishlist_style"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step6">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 7: Offer Display Style --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.offer_display_style', 'Offer Display Style') }}</h5>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-8 col-lg-7">
                                            <div class="mb-3">
                                                <label class="form-label" for="">
                                                    {{ labels('admin_labels.slider_style', 'Offer Style') }}
                                                    <i class="fas fa-info-circle text-secondary ms-1"
                                                        data-bs-toggle="popover" data-bs-placement="right"
                                                        data-bs-content="{{ labels('admin_labels.popover_choose_offer_slider_style', 'Choose the style for offer sliders.') }}"></i>
                                                </label>
                                                <select class="slider_style form-control form-select"
                                                    name="offer_slider_style">
                                                    <option value="slider_style_1">{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="slider_style_2">{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="slider_style_3">{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                    <option value="slider_style_4">{{ labels('admin_labels.style_4', 'Style 4') }}</option>
                                                    <option value="slider_style_5">{{ labels('admin_labels.style_5', 'Style 5') }}</option>
                                                </select>
                                            </div>
                                            <div class="slider_style_images store_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_1.png') }}"
                                                    class="slider_style_1" alt="Offer Slider Style 1" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_2.png') }}"
                                                    class="slider_style_2" alt="Offer Slider Style 2" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_3.png') }}"
                                                    class="slider_style_3" alt="Offer Slider Style 3" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_4.png') }}"
                                                    class="slider_style_4" alt="Offer Slider Style 4" />
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_style_1.png') }}"
                                                    class="slider_style_5" alt="Offer Style 1" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="store-next-button" data-step="step7">
                            <button type="button" class="btn btn-primary muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.next', 'Next') }}</span>
                                <svg class="muqtarin-next-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- STEP 8: Delivery Charge & Product Deliverability Setting --}}
                    <div class="card2">
                        <div class="row">
                            <div class="col col-xxl-12">
                                <div class="card muqtarin-wizard-card">
                                    <div class="muqtarin-step-header">
                                        <span class="muqtarin-step-header-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                                        </span>
                                        <h5>{{ labels('admin_labels.delivery_charge_and_product_deliverability_setting', 'Delivery Charge & Product Deliverability Setting') }}</h5>
                                    </div>

                                    <!-- Product Deliverability Settings -->
                                    <div class="muqtarin-subpanel mt-0">
                                        <h6>{{ labels('admin_labels.product_deliverability_setting', 'Product Deliverability Setting') }}</h6>
                                        <input type="hidden" name="product_deliverability_type_value">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="muqtarin-option-card">
                                                    <label for="zipcode_wise_deliverability_switch">
                                                        {{ labels('admin_labels.zipcode_wise_deliverability', 'Zipcode Wise Deliverability') }}
                                                        <i class="fa fa-info-circle text-secondary ms-1"
                                                            data-bs-toggle="popover" data-bs-placement="right"
                                                            data-bs-content="{{ labels('admin_labels.popover_zipcode_wise_deliverability', 'Enable zipcode-based product deliverability.') }}"></i>
                                                    </label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="radio"
                                                            id="zipcode_wise_deliverability_switch"
                                                            name="product_deliverability">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="muqtarin-option-card">
                                                    <div>
                                                        <label for="city_wise_deliverability_switch">
                                                            {{ labels('admin_labels.city_wise_deliverability', 'City Wise Deliverability') }}
                                                            <i class="fa fa-info-circle text-secondary ms-1"
                                                                data-bs-toggle="popover" data-bs-placement="right"
                                                                data-bs-content="{{ labels('admin_labels.popover_city_wise_deliverability', 'Enable city-based product deliverability.') }}"></i>
                                                        </label>
                                                        @if (isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1)
                                                            <div class="text-danger small mt-1">
                                                                {{ labels('admin_labels.shipping_method_disabled_due_to_standard_shipping', '(Disabled because standard shipping is on from shipping method)') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="radio"
                                                            id="city_wise_deliverability_switch"
                                                            name="product_deliverability"
                                                            <?= isset($shipping_settings['shiprocket_shipping_method']) && $shipping_settings['shiprocket_shipping_method'] == 1 ? 'disabled' : '' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!$shiprocketEnabled)
                                        <!-- Delivery charge settings -->
                                        <div class="muqtarin-subpanel">
                                            <h6>{{ labels('admin_labels.delivery_charge_setting', 'Delivery Charge Setting') }}</h6>
                                            <input type="hidden" name="delivery_charge_type_value">
                                            <div class="row g-3">
                                                <div class="col-md-4 zipcode_wise_delivery_charge">
                                                    <div class="muqtarin-option-card">
                                                        <label for="zipcode_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.zipcode_wise', 'Zipcode Wise') }}
                                                            <i class="fa fa-info-circle text-secondary ms-1"
                                                                data-bs-toggle="popover" data-bs-placement="right"
                                                                data-bs-content="{{ labels('admin_labels.popover_zipcode_wise_delivery_charge', 'Set delivery charges based on zipcode.') }}"></i>
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="zipcode_wise_delivery_charge_switch"
                                                                name="delivery_charge_type">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 city_wise_delivery_charge d-none">
                                                    <div class="muqtarin-option-card">
                                                        <label for="city_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.city_wise', 'City Wise') }}
                                                            <i class="fa fa-info-circle text-secondary ms-1"
                                                                data-bs-toggle="popover" data-bs-placement="right"
                                                                data-bs-content="{{ labels('admin_labels.popover_city_wise_delivery_charge', 'Set delivery charges based on city.') }}"></i>
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="city_wise_delivery_charge_switch"
                                                                name="delivery_charge_type">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="muqtarin-option-card">
                                                        <label for="product_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.product_wise', 'Product Wise') }}
                                                            <i class="fa fa-info-circle text-secondary ms-1"
                                                                data-bs-toggle="popover" data-bs-placement="right"
                                                                data-bs-content="{{ labels('admin_labels.popover_product_wise_delivery_charge', 'Set delivery charges per product.') }}"></i>
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="product_wise_delivery_charge_switch"
                                                                name="delivery_charge_type">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="muqtarin-option-card">
                                                        <label for="global_delivery_charge_switch">
                                                            {{ labels('admin_labels.global_delivery_charge', 'Global Delivery Charge') }}
                                                            <i class="fa fa-info-circle text-secondary ms-1"
                                                                data-bs-toggle="popover" data-bs-placement="right"
                                                                data-bs-content="{{ labels('admin_labels.popover_global_delivery_charge', 'Set a global delivery charge for all orders.') }}"></i>
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="global_delivery_charge_switch"
                                                                name="delivery_charge_type">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3 mt-1">
                                                <div class="col-md-6 d-none" id="delivery_charge_amount_field">
                                                    <label class="form-label" for="basic-default-fullname">
                                                        {{ labels('admin_labels.delivery_charge', 'Delivery Charge') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                        <i class="fa fa-info-circle text-secondary ms-1"
                                                            data-bs-toggle="popover" data-bs-placement="right"
                                                            data-bs-content="{{ labels('admin_labels.popover_delivery_charge_amount', 'Enter the delivery charge amount.') }}"></i>
                                                    </label>
                                                    <input type="number" min=0 class="form-control"
                                                        id="basic-default-fullname" placeholder=""
                                                        name="delivery_charge_amount" value="">
                                                </div>
                                                <div class="col-md-6 d-none" id="minimum_free_delivery_amount_field">
                                                    <label class="form-label" for="basic-default-fullname">
                                                        {{ labels('admin_labels.minimum_free_delivery_amount', 'Minimum Amount for Free Delivery') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                        <i class="fa fa-info-circle text-secondary ms-1"
                                                            data-bs-toggle="popover" data-bs-placement="right"
                                                            data-bs-content="{{ labels('admin_labels.popover_minimum_free_delivery_amount', 'Set the minimum order amount for free delivery.') }}"></i>
                                                    </label>
                                                    <input type="number" min=0 class="form-control"
                                                        id="basic-default-fullname" placeholder=""
                                                        name="minimum_free_delivery_amount" value="">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="muqtarin-submit-wrap">
                            <button type="submit" class="btn btn-primary submit_button muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.submit', 'Submit') }}</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="muqtarin-prev-dock">
                    <p class="prev btn reset-btn" style="display: none;">{{ labels('admin_labels.go_back', 'Go Back') }}</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var wizard = document.querySelector('.muqtarin-store-wizard');
            if (!wizard) return;

            var cards = Array.prototype.slice.call(wizard.querySelectorAll('.card2'));
            var steps = Array.prototype.slice.call(wizard.querySelectorAll('#store_progressbar li.step0'));
            var counterEl = document.getElementById('muqtarin-wizard-step-counter');
            var pctEl = document.getElementById('muqtarin-wizard-pct');
            var fillEl = document.getElementById('muqtarin-wizard-progress-fill');
            var prevBtn = wizard.querySelector('.prev');
            var lastIdx = -1;

            function scrollWizardIntoView() {
                var rect = wizard.getBoundingClientRect();
                if (rect.top < 72) {
                    window.scrollTo({
                        top: Math.max(0, window.scrollY + rect.top - 84),
                        behavior: 'smooth'
                    });
                }
            }

            function syncWizardState() {
                var currentIdx = 0;
                for (var i = 0; i < cards.length; i++) {
                    if (cards[i].classList.contains('show')) {
                        currentIdx = i;
                        break;
                    }
                }

                for (var k = 0; k < cards.length; k++) {
                    cards[k].style.display = '';
                }

                for (var j = 0; j < steps.length; j++) {
                    if (j < currentIdx) {
                        steps[j].setAttribute('data-state', 'completed');
                        steps[j].classList.add('active');
                    } else if (j === currentIdx) {
                        steps[j].setAttribute('data-state', 'current');
                        steps[j].classList.add('active');
                    } else {
                        steps[j].setAttribute('data-state', 'upcoming');
                        steps[j].classList.remove('active');
                    }
                }

                if (prevBtn) {
                    prevBtn.style.display = currentIdx === 0 ? 'none' : 'block';
                }

                var pct = Math.round(((currentIdx + 1) / cards.length) * 100);
                if (counterEl) counterEl.textContent = (currentIdx + 1) + ' / ' + cards.length;
                if (pctEl) pctEl.textContent = pct + '%';
                if (fillEl) fillEl.style.width = pct + '%';

                if (lastIdx !== -1 && lastIdx !== currentIdx) {
                    scrollWizardIntoView();
                }
                lastIdx = currentIdx;
            }

            // Observe class changes made by stepper.js on .card2
            var cardObs = new MutationObserver(function() {
                syncWizardState();
            });
            cards.forEach(function(card) {
                cardObs.observe(card, { attributes: true, attributeFilter: ['class'] });
            });

            // Allow clicking completed steps in the rail to jump back directly
            steps.forEach(function(stepEl, targetIdx) {
                stepEl.addEventListener('click', function() {
                    if (stepEl.getAttribute('data-state') !== 'completed') return;
                    cards.forEach(function(card, idx) {
                        card.style.display = '';
                        if (idx === targetIdx) {
                            card.classList.add('show');
                        } else {
                            card.classList.remove('show');
                        }
                    });
                    syncWizardState();
                });
            });

            // Keep color swatch in sync even before user clicks text box
            ['light_theme_color', 'dark_theme_color', 'hover_color', 'active_color', 'background_color'].forEach(function(id) {
                var codeEl = document.getElementById(id + '_code');
                var pickerEl = document.getElementById(id);
                if (codeEl && pickerEl && codeEl.value && /^#[0-9A-Fa-f]{6}$/.test(codeEl.value)) {
                    pickerEl.value = codeEl.value;
                }
            });

            syncWizardState();
        });
    </script>
@endsection
