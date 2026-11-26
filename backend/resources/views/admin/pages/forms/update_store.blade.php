@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_store', 'Update Store') }}
@endsection
@section('content')
    <x-admin.breadcrumb :title="labels('admin_labels.update_store', 'Update Store')" :subtitle="labels(
        'admin_labels.lets_unlesh_a_wave_of_new_stores_on_your_marketplace',
        'Let’s unleash a wave of new stores on your marketplace.',
    )" :breadcrumbs="[['label' => labels('admin_labels.update_store', 'Update Store')]]" />

    @php
        $store_settings = is_array($data->store_settings)
            ? $data->store_settings
            : (json_decode($data->store_settings ?? '[]', true) ?: (array) ($data->store_settings ?? []));
        use App\Services\MediaService;
        use App\Services\SettingService;
        $shipping_settings = app(SettingService::class)->getSettings('shipping_method', true);
        $shipping_settings = json_decode($shipping_settings, true);
        $shiprocketEnabled =
            isset($shipping_settings['shiprocket_shipping_method']) &&
            $shipping_settings['shiprocket_shipping_method'] == 1;
        $decodedName = isset($data->name) ? (json_decode($data->name, true) ?: []) : [];
        $decodedDesc = isset($data->description) ? (json_decode($data->description, true) ?: []) : [];
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
                <form action="{{ url('/admin/store/update/' . $data->id) }}" enctype="multipart/form-data" method="POST"
                    class="submit_form">
                    @method('PUT')
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
                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <li class="nav-item" role="presentation">
                                                    <button class="language-nav-link nav-link"
                                                        id="tab-{{ $lang->code }}" data-bs-toggle="tab"
                                                        data-bs-target="#content-{{ $lang->code }}" type="button"
                                                        role="tab" aria-controls="content-{{ $lang->code }}"
                                                        aria-selected="false">
                                                        {{ $lang->language }}
                                                    </button>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    <div class="tab-content mt-3" id="UpdatebrandTabsContent">
                                        <div class="tab-pane fade show active" id="content-en" role="tabpanel"
                                            aria-labelledby="tab-en">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="brand_name" class="form-label">
                                                        {{ labels('admin_labels.name', 'Name') }}
                                                        <span class="text-asterisks text-sm">*</span>
                                                        <i class="fas fa-info-circle text-secondary ms-1"
                                                            data-bs-toggle="popover"
                                                            data-bs-content="{{ labels('admin_labels.store_name_instruction', 'Enter the store name as it will appear to customers.') }}"></i>
                                                    </label>
                                                    <input type="text" class="form-control"
                                                        placeholder="{{ labels('admin_labels.store_name_placeholder', 'MUQTARIN Store') }}"
                                                        name="name"
                                                        value="{{ $decodedName['en'] ?? '' }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="short_description" class="form-label">
                                                        {{ labels('admin_labels.description', 'Description') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                    </label>
                                                    <input type="text" class="form-control" name="description"
                                                        value="{{ $decodedDesc['en'] ?? '' }}"
                                                        placeholder="{{ labels('admin_labels.description_placeholder', 'Description') }}">
                                                </div>
                                            </div>
                                        </div>

                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <div class="tab-pane fade" id="content-{{ $lang->code }}"
                                                    role="tabpanel" aria-labelledby="tab-{{ $lang->code }}">
                                                    <div class="row g-3">
                                                        <div class="col-md-12">
                                                            <label for="translated_title_{{ $lang->code }}"
                                                                class="form-label">
                                                                {{ labels('admin_labels.name', 'Name') }}
                                                                ({{ $lang->language }})
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                name="translated_store_name[{{ $lang->code }}]"
                                                                value="{{ $decodedName[$lang->code] ?? '' }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="translated_short_description_{{ $lang->code }}"
                                                                class="form-label">
                                                                {{ labels('admin_labels.description', 'Description') }}
                                                                ({{ $lang->language }})
                                                            </label>
                                                            <input type="text" class="form-control"
                                                                name="translated_store_description[{{ $lang->code }}]"
                                                                value="{{ $decodedDesc[$lang->code] ?? '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <div class="muqtarin-switch-card">
                                                @php $singleSellerLabel = labels('admin_labels.single_seller_order_system', 'Single Seller Order System'); @endphp
                                                <label for="is_single_seller_order_system">
                                                    {{ $singleSellerLabel }}@if(!str_ends_with($singleSellerLabel, '?') && !str_ends_with($singleSellerLabel, '؟'))?@endif
                                                    <small class="text-muted">({{ labels('admin_labels.for_cart', 'For Cart') }})</small>
                                                </label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="is_single_seller_order_system"
                                                        {{ ($data->is_single_seller_order_system ?? '') == '1' ? 'checked' : '' }}
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
                                            placeholder="{{ labels('admin_labels.place_food_license_in_other_documents', 'Please attach the food license/pharmacy license in the other documents') }}">{{ $data->note_for_necessary_documents ?? '' }}</textarea>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="muqtarin-color-card">
                                                <label for="light_theme_color_code">
                                                    {{ labels('admin_labels.primary_theme_color', 'Primary Theme Color') }}
                                                    <span class="text-asterisks text-sm">*</span>
                                                </label>
                                                <div class="muqtarin-color-control">
                                                    <input type="color"
                                                        value="{{ !empty($data->primary_color) ? $data->primary_color : '#e0ffee' }}"
                                                        id="light_theme_color"
                                                        oninput="updateColorCode('light_theme_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="light_theme_color_code" name="primary_color"
                                                        class="form-control muqtarin-color-hex"
                                                        oninput="updateColorPicker('light_theme_color', this.value)"
                                                        value="{{ !empty($data->primary_color) ? $data->primary_color : '' }}">
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
                                                    <input type="color"
                                                        value="{{ !empty($data->secondary_color) ? $data->secondary_color : '#e0ffee' }}"
                                                        id="dark_theme_color"
                                                        oninput="updateColorCode('dark_theme_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="dark_theme_color_code"
                                                        oninput="updateColorPicker('dark_theme_color', this.value)"
                                                        name="secondary_color" class="form-control muqtarin-color-hex"
                                                        value="{{ !empty($data->secondary_color) ? $data->secondary_color : '' }}">
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
                                                    <input type="color"
                                                        value="{{ !empty($data->hover_color) ? $data->hover_color : '#e0ffee' }}"
                                                        id="hover_color" oninput="updateColorCode('hover_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="hover_color_code"
                                                        oninput="updateColorPicker('hover_color', this.value)"
                                                        name="hover_color" class="form-control muqtarin-color-hex"
                                                        value="{{ !empty($data->hover_color) ? $data->hover_color : '' }}">
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
                                                    <input type="color"
                                                        value="{{ !empty($data->active_color) ? $data->active_color : '#e0ffee' }}"
                                                        id="active_color" oninput="updateColorCode('active_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="active_color_code"
                                                        oninput="updateColorPicker('active_color', this.value)"
                                                        name="active_color" class="form-control muqtarin-color-hex"
                                                        value="{{ !empty($data->active_color) ? $data->active_color : '' }}">
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
                                                    <input type="color"
                                                        value="{{ !empty($data->background_color) ? $data->background_color : '#e0ffee' }}"
                                                        id="background_color"
                                                        oninput="updateColorCode('background_color')"
                                                        class="color_picker muqtarin-color-swatch">
                                                    <input type="text" id="background_color_code"
                                                        oninput="updateColorPicker('background_color', this.value)"
                                                        name="background_color" class="form-control muqtarin-color-hex"
                                                        value="{{ !empty($data->background_color) ? $data->background_color : '' }}">
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
                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label for="image">{{ labels('admin_labels.image', 'Image') }}</label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond" name="update_image"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->image && !empty($data->image))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->image, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="image" value="{{ $data->image }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label for="banner_image" class="form-label">
                                                    {{ labels('admin_labels.banner_image', 'Banner Image') }}
                                                </label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond" name="update_banner_image"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->banner_image && !empty($data->banner_image))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->banner_image, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="banner_image" value="{{ $data->banner_image }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
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

                                    @php
                                        $selectedWebTheme = $store_settings['web_home_page_theme'] ?? 'web_home_page_theme_1';
                                    @endphp
                                    <div class="mb-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="web_home_page_theme">
                                                {{ labels('admin_labels.web_home_page_theme', 'Web Home Page Theme') }}
                                            </label>
                                            <select class="web_home_page_theme form-control form-select"
                                                id="web_home_page_theme"
                                                name="web_home_page_theme">
                                                <option value="web_home_page_theme_muqtarin_editorial" {{ $selectedWebTheme === 'web_home_page_theme_muqtarin_editorial' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_editorial', 'MUQTARIN Editorial') }}</option>
                                                <option value="web_home_page_theme_muqtarin_bento" {{ $selectedWebTheme === 'web_home_page_theme_muqtarin_bento' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_bento', 'MUQTARIN Bento Market') }}</option>
                                                <option value="web_home_page_theme_muqtarin_glass" {{ $selectedWebTheme === 'web_home_page_theme_muqtarin_glass' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_glass', 'MUQTARIN Glass Atelier') }}</option>
                                                <option value="web_home_page_theme_muqtarin_express" {{ $selectedWebTheme === 'web_home_page_theme_muqtarin_express' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_express', 'MUQTARIN Express') }}</option>
                                                <option value="web_home_page_theme_1" {{ $selectedWebTheme === 'web_home_page_theme_1' ? 'selected' : '' }}>{{ labels('admin_labels.theme_1', 'Theme 1') }}</option>
                                                <option value="web_home_page_theme_2" {{ $selectedWebTheme === 'web_home_page_theme_2' ? 'selected' : '' }}>{{ labels('admin_labels.theme_2', 'Theme 2') }}</option>
                                                <option value="web_home_page_theme_3" {{ $selectedWebTheme === 'web_home_page_theme_3' ? 'selected' : '' }}>{{ labels('admin_labels.theme_3', 'Theme 3') }}</option>
                                                <option value="web_home_page_theme_4" {{ $selectedWebTheme === 'web_home_page_theme_4' ? 'selected' : '' }}>{{ labels('admin_labels.theme_4', 'Theme 4') }}</option>
                                                <option value="web_home_page_theme_5" {{ $selectedWebTheme === 'web_home_page_theme_5' ? 'selected' : '' }}>{{ labels('admin_labels.theme_5', 'Theme 5') }}</option>
                                                <option value="web_home_page_theme_6" {{ $selectedWebTheme === 'web_home_page_theme_6' ? 'selected' : '' }}>{{ labels('admin_labels.theme_6', 'Theme 6') }}</option>
                                            </select>
                                        </div>
                                        <div class="web_home_page_theme_images store_style_box home_theme_style_box">
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_muqtarin_editorial.png') }}"
                                                class="web_home_page_theme_muqtarin_editorial home_theme" alt="{{ labels('admin_labels.muqtarin_editorial', 'MUQTARIN Editorial') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_muqtarin_bento.png') }}"
                                                class="web_home_page_theme_muqtarin_bento home_theme" alt="{{ labels('admin_labels.muqtarin_bento', 'MUQTARIN Bento Market') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_muqtarin_glass.png') }}"
                                                class="web_home_page_theme_muqtarin_glass home_theme" alt="{{ labels('admin_labels.muqtarin_glass', 'MUQTARIN Glass Atelier') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_muqtarin_express.png') }}"
                                                class="web_home_page_theme_muqtarin_express home_theme" alt="{{ labels('admin_labels.muqtarin_express', 'MUQTARIN Express') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_1.png') }}"
                                                class="web_home_page_theme_1 home_theme" alt="{{ labels('admin_labels.theme_1', 'Theme 1') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_2.png') }}"
                                                class="web_home_page_theme_2 home_theme" alt="{{ labels('admin_labels.theme_2', 'Theme 2') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_3.png') }}"
                                                class="web_home_page_theme_3 home_theme" alt="{{ labels('admin_labels.theme_3', 'Theme 3') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_4.png') }}"
                                                class="web_home_page_theme_4 home_theme" alt="{{ labels('admin_labels.theme_4', 'Theme 4') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_5.png') }}"
                                                class="web_home_page_theme_5 home_theme" alt="{{ labels('admin_labels.theme_5', 'Theme 5') }}" />
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/theme_6.png') }}"
                                                class="web_home_page_theme_6 home_theme" alt="{{ labels('admin_labels.theme_6', 'Theme 6') }}" />
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.app_images', 'App Images') }}</h5>
                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label>{{ labels('admin_labels.banner_image_for_most_selling_products', 'Banner Image (For Most Selling Products (390 x 500))') }}</label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond"
                                                            name="update_banner_image_for_most_selling_product"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->banner_image_for_most_selling_product && !empty($data->banner_image_for_most_selling_product))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->banner_image_for_most_selling_product, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="banner_image_for_most_selling_product"
                                                                        value="{{ $data->banner_image_for_most_selling_product }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label class="form-label">{{ labels('admin_labels.stack_image', 'Stack Image') }} (App Home Page Background Image (390 x 501))</label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond" name="update_stack_image"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->stack_image && !empty($data->stack_image))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->stack_image, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="stack_image" value="{{ $data->stack_image }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label>{{ labels('admin_labels.login_page_image', 'Login Page Image (App Login Page Background Image)') }}</label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond" name="update_login_image"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->login_image && !empty($data->login_image))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->login_image, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="login_image" value="{{ $data->login_image }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="muqtarin-upload-tile">
                                                <label>{{ labels('admin_labels.half_store_logo', 'Half Store Logo (try to upload square image (100 x 100))') }}</label>
                                                <div class="row g-3 align-items-center">
                                                    <div class="col-md-8 text-center form-group mb-0">
                                                        <input type="file" class="filepond" name="update_half_store_logo"
                                                            data-max-file-size="30MB" data-max-files="20"
                                                            accept="image/*,.webp" />
                                                    </div>
                                                    @if ($data->half_store_logo && !empty($data->half_store_logo))
                                                        <div class="col-md-4">
                                                            <label class="text-danger small d-block mb-2">*Only Choose When Update is necessary</label>
                                                            <div class="image-upload-section">
                                                                <div class="shadow-sm p-3 bg-white rounded text-center grow image border">
                                                                    <div class='image-upload-div'>
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ route('admin.dynamic_image', [
                                                                                'url' => app(MediaService::class)->getMediaImageUrl($data->half_store_logo, 'STORE_IMG_PATH'),
                                                                                'width' => 150,
                                                                                'quality' => 90,
                                                                            ]) }}"
                                                                            alt="{{ labels('admin_labels.not_found_alt', 'Not Found') }}" />
                                                                    </div>
                                                                    <input type="hidden" name="half_store_logo" value="{{ $data->half_store_logo }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="col-md-4 image-upload-section">
                                                            <div class="p-3 bg-white rounded text-center grow image d-none store-image-container"></div>
                                                        </div>
                                                    @endif
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
                                                <select class="feature_section_header_style form-select form-control"
                                                    name="store_style">
                                                    <option value="header_style_1" {{ ($store_settings['store_style'] ?? '') === 'header_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="header_style_2" {{ ($store_settings['store_style'] ?? '') === 'header_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="header_style_3" {{ ($store_settings['store_style'] ?? '') === 'header_style_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="feature_section_header_style_images store_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_1.png') }}"
                                                    class="header_style_1" alt="Store Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_2.png') }}"
                                                    class="header_style_2" alt="Store Style 2">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/store_style_3.png') }}"
                                                    class="header_style_3" alt="Store Style 3">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    {{ labels('admin_labels.product_display_style', 'Products display style') }}
                                                </label>
                                                <select class="product_card_style form-select form-control"
                                                    name="product_style">
                                                    <option value="style_1" {{ ($store_settings['product_style'] ?? '') === 'style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="style_2" {{ ($store_settings['product_style'] ?? '') === 'style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="style_3" {{ ($store_settings['product_style'] ?? '') === 'style_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="product_card_style_images product_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_1.png') }}"
                                                    class="style_1" alt="Product Card Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_2.png') }}"
                                                    class="style_2" alt="Product Card Style 2">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/product_card_style_3.png') }}"
                                                    class="style_3" alt="Product Card Style 3">
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.cards_display_style', 'Cards Display Style For Web') }}</h5>
                                    <div class="mb-4">
                                        <div class="mb-3">
                                            <label class="form-label" for="products_display_style_for_web">
                                                {{ labels('admin_labels.products_display_style_for_web', 'Products display style') }}
                                            </label>
                                            <select class="products_display_style_for_web form-control form-select"
                                                id="products_display_style_for_web"
                                                name="products_display_style_for_web">
                                                <option value="products_display_style_for_web_6" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_6' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_editorial_card', 'MUQTARIN Editorial') }}</option>
                                                <option value="products_display_style_for_web_7" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_7' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_express_card', 'MUQTARIN Express') }}</option>
                                                <option value="products_display_style_for_web_1" {{ ($store_settings['products_display_style_for_web'] ?? 'products_display_style_for_web_1') === 'products_display_style_for_web_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                <option value="products_display_style_for_web_2" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                <option value="products_display_style_for_web_3" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                <option value="products_display_style_for_web_4" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_4' ? 'selected' : '' }}>{{ labels('admin_labels.style_4', 'Style 4') }}</option>
                                                <option value="products_display_style_for_web_5" {{ ($store_settings['products_display_style_for_web'] ?? '') === 'products_display_style_for_web_5' ? 'selected' : '' }}>{{ labels('admin_labels.style_5', 'Style 5') }}</option>
                                            </select>
                                        </div>
                                        <iframe class="overflow-hidden muqtarin-clean-preview-iframe"
                                            id="products_display_style_for_web_iframe"
                                            src="{{ url('admin/web_product_card_style') }}"></iframe>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.products_details_style', 'Products Details Style For Web') }}</h5>
                                    <div>
                                        <div class="mb-3">
                                            <label class="form-label" for="web_product_details_style">
                                                {{ labels('admin_labels.products_details_style_for_web', 'Products details display style') }}
                                            </label>
                                            <select class="web_product_details_style form-control form-select"
                                                id="web_product_details_style"
                                                name="web_product_details_style">
                                                <option value="web_product_details_style_3" {{ ($store_settings['web_product_details_style'] ?? '') === 'web_product_details_style_3' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_editorial_details', 'MUQTARIN Editorial PDP') }}</option>
                                                <option value="web_product_details_style_1" {{ ($store_settings['web_product_details_style'] ?? 'web_product_details_style_1') === 'web_product_details_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                <option value="web_product_details_style_2" {{ ($store_settings['web_product_details_style'] ?? '') === 'web_product_details_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                            </select>
                                        </div>
                                        <div class="web_product_details_style_images store_style_box home_theme_style_box">
                                            <img src="{{ app(MediaService::class)->getImageUrl('system_images/details_3.png') }}"
                                                class="web_product_details_style_3" alt="MUQTARIN Editorial PDP" />
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
                                                    for="category_section_title">{{ labels('admin_labels.categories_section_title', 'Categories Section Title') }}<span
                                                        class='text-asterisks text-sm'>*</span></label>
                                                <input type="text" class="form-control"
                                                    id="category_section_title"
                                                    placeholder="{{ labels('admin_labels.shop_by_categories_placeholder', 'Shop By Categories') }}"
                                                    name="category_section_title"
                                                    value="{{ isset($store_settings['category_section_title'])
                                                        ? (is_array($store_settings['category_section_title'])
                                                            ? $store_settings['category_section_title']['en'] ?? ''
                                                            : $store_settings['category_section_title'])
                                                        : '' }}">
                                            </div>
                                        </div>

                                        @foreach ($languages as $lang)
                                            @if ($lang->code !== 'en')
                                                <div class="tab-pane fade" id="categorycontent-{{ $lang->code }}"
                                                    role="tabpanel" aria-labelledby="tab-{{ $lang->code }}">
                                                    <div class="mb-3">
                                                        <label
                                                            for="translated_categories_section_title_{{ $lang->code }}"
                                                            class="form-label">
                                                            {{ labels('admin_labels.categories_section_title', 'Categories Section Title') }}
                                                            ({{ $lang->language }})
                                                        </label>
                                                        <input type="text" class="form-control"
                                                            name="translated_categories_section_title[{{ $lang->code }}]"
                                                            value="{{ isset($store_settings['category_section_title']) && is_array($store_settings['category_section_title']) ? $store_settings['category_section_title'][$lang->code] ?? '' : '' }}">
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
                                                    <option value="category_style_1" {{ ($store_settings['category_style'] ?? '') === 'category_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="category_style_2" {{ ($store_settings['category_style'] ?? '') === 'category_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                </select>
                                            </div>
                                            <div class="categories_style_images category_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_style_1.png') }}"
                                                    class="category_style_1" alt="Category Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_style_2.png') }}"
                                                    class="category_style_2" alt="Category Style 2">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    {{ labels('admin_labels.categories_card_style', 'Categories Cards Style') }}
                                                </label>
                                                <select class="categories_card_style form-control form-select"
                                                    name="category_card_style">
                                                    <option value="category_card_style_1" {{ ($store_settings['category_card_style'] ?? '') === 'category_card_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="category_card_style_2" {{ ($store_settings['category_card_style'] ?? '') === 'category_card_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="category_card_style_3" {{ ($store_settings['category_card_style'] ?? '') === 'category_card_style_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <div class="categories_card_style_images category_card_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_1.jpg') }}"
                                                    class="category_card_style_1" alt="Category Card Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_2.jpg') }}"
                                                    class="category_card_style_2" alt="Category Card Style 2">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/categories_cards_style_3.jpg') }}"
                                                    class="category_card_style_3" alt="Category Card Style 3">
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.cards_display_style', 'Cards Display Style For Web') }}</h5>
                                    <div>
                                        <div class="mb-3">
                                            <label class="form-label" for="categories_display_style_for_web">
                                                {{ labels('admin_labels.categories_display_style', 'Categories Display Style') }}
                                            </label>
                                            <select class="categories_display_style_for_web form-control form-select"
                                                id="categories_display_style_for_web"
                                                name="categories_display_style_for_web">
                                                <option value="categories_display_style_for_web_4" {{ ($store_settings['categories_display_style_for_web'] ?? '') === 'categories_display_style_for_web_4' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_bento_categories', 'MUQTARIN Bento') }}</option>
                                                <option value="categories_display_style_for_web_1" {{ ($store_settings['categories_display_style_for_web'] ?? 'categories_display_style_for_web_1') === 'categories_display_style_for_web_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                <option value="categories_display_style_for_web_2" {{ ($store_settings['categories_display_style_for_web'] ?? '') === 'categories_display_style_for_web_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                <option value="categories_display_style_for_web_3" {{ ($store_settings['categories_display_style_for_web'] ?? '') === 'categories_display_style_for_web_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                            </select>
                                        </div>
                                        <iframe class="overflow-hidden muqtarin-clean-preview-iframe"
                                            id="categories_display_style_for_web_iframe"
                                            src="{{ url('admin/web_categories_style') }}"></iframe>
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
                                                <label class="form-label" for="brand_style">
                                                    {{ labels('admin_labels.brand_style', 'Brand Style') }}
                                                </label>
                                                <select class="brands_style form-control form-select"
                                                    name="brand_style">
                                                    <option value="brands_style_1" {{ ($store_settings['brand_style'] ?? '') === 'brands_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="brands_style_2" {{ ($store_settings['brand_style'] ?? '') === 'brands_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                </select>
                                            </div>
                                            <div class="brands_style_images category_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/brands_style_1.png') }}"
                                                    class="brands_style_1" alt="Brands Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/brands_style_2.png') }}"
                                                    class="brands_style_2" alt="Brands Style 2">
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="muqtarin-sub-heading">{{ labels('admin_labels.brands_display_style', 'Brands & Wishlist Display Style For Web') }}</h5>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="brands_display_style_for_web">
                                                    {{ labels('admin_labels.brands_display_style', 'Brands Display Style') }}
                                                </label>
                                                <select class="brands_display_style_for_web form-control form-select"
                                                    id="brands_display_style_for_web"
                                                    name="brands_display_style_for_web">
                                                    <option value="brands_display_style_for_web_4" {{ ($store_settings['brands_display_style_for_web'] ?? '') === 'brands_display_style_for_web_4' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_brands_grid', 'MUQTARIN Showcase') }}</option>
                                                    <option value="brands_display_style_for_web_1" {{ ($store_settings['brands_display_style_for_web'] ?? 'brands_display_style_for_web_1') === 'brands_display_style_for_web_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="brands_display_style_for_web_2" {{ ($store_settings['brands_display_style_for_web'] ?? '') === 'brands_display_style_for_web_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="brands_display_style_for_web_3" {{ ($store_settings['brands_display_style_for_web'] ?? '') === 'brands_display_style_for_web_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                </select>
                                            </div>
                                            <iframe class="overflow-hidden muqtarin-clean-preview-iframe"
                                                id="brands_display_style_for_web_iframe"
                                                src="{{ url('admin/web_brands_style') }}"></iframe>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="wishlist_display_style_for_web">
                                                    {{ labels('admin_labels.wishlist_display_style', 'Wishlist Display Style') }}
                                                </label>
                                                <select class="wishlist_display_style_for_web form-control form-select"
                                                    id="wishlist_display_style_for_web"
                                                    name="wishlist_display_style_for_web">
                                                    <option value="wishlist_display_style_for_web_3" {{ ($store_settings['wishlist_display_style_for_web'] ?? '') === 'wishlist_display_style_for_web_3' ? 'selected' : '' }}>{{ labels('admin_labels.muqtarin_wishlist_grid', 'MUQTARIN Editorial Grid') }}</option>
                                                    <option value="wishlist_display_style_for_web_1" {{ ($store_settings['wishlist_display_style_for_web'] ?? 'wishlist_display_style_for_web_1') === 'wishlist_display_style_for_web_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="wishlist_display_style_for_web_2" {{ ($store_settings['wishlist_display_style_for_web'] ?? '') === 'wishlist_display_style_for_web_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                </select>
                                            </div>
                                            <iframe class="overflow-hidden muqtarin-clean-preview-iframe"
                                                id="wishlist_display_style_for_web_iframe"
                                                src="{{ url('admin/web_wishlist_style') }}"></iframe>
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
                                                <label class="form-label">
                                                    {{ labels('admin_labels.slider_style', 'Offer Style') }}
                                                </label>
                                                <select class="slider_style form-control form-select"
                                                    name="offer_slider_style">
                                                    <option value="slider_style_1" {{ ($store_settings['offer_slider_style'] ?? '') === 'slider_style_1' ? 'selected' : '' }}>{{ labels('admin_labels.style_1', 'Style 1') }}</option>
                                                    <option value="slider_style_2" {{ ($store_settings['offer_slider_style'] ?? '') === 'slider_style_2' ? 'selected' : '' }}>{{ labels('admin_labels.style_2', 'Style 2') }}</option>
                                                    <option value="slider_style_3" {{ ($store_settings['offer_slider_style'] ?? '') === 'slider_style_3' ? 'selected' : '' }}>{{ labels('admin_labels.style_3', 'Style 3') }}</option>
                                                    <option value="slider_style_4" {{ ($store_settings['offer_slider_style'] ?? '') === 'slider_style_4' ? 'selected' : '' }}>{{ labels('admin_labels.style_4', 'Style 4') }}</option>
                                                    <option value="slider_style_5" {{ ($store_settings['offer_slider_style'] ?? '') === 'slider_style_5' ? 'selected' : '' }}>{{ labels('admin_labels.style_5', 'Style 5') }}</option>
                                                </select>
                                            </div>
                                            <div class="slider_style_images store_style_box">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_1.png') }}"
                                                    class="slider_style_1" alt="Offer Slider Style 1">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_2.png') }}"
                                                    class="slider_style_2" alt="Offer Slider Style 2">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_3.png') }}"
                                                    class="slider_style_3" alt="Offer Slider Style 3">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_slider_section_style_4.png') }}"
                                                    class="slider_style_4" alt="Offer Slider Style 4">
                                                <img src="{{ app(MediaService::class)->getImageUrl('system_images/offer_style_1.png') }}"
                                                    class="slider_style_5" alt="Offer Style 1">
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
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">{{ labels('admin_labels.product_deliverability_setting', 'Product Deliverability Setting') }}</h6>
                                            <a href="{{ route('admin.zones.index') }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </div>
                                        <input type="hidden" name="product_deliverability_type_value"
                                            value="{{ $data->product_deliverability_type ?? '' }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="muqtarin-option-card">
                                                    <label for="zipcode_wise_deliverability_switch">
                                                        {{ labels('admin_labels.zipcode_wise_deliverability', 'Zipcode Wise Deliverability') }}
                                                    </label>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="radio"
                                                            id="zipcode_wise_deliverability_switch"
                                                            name="product_deliverability"
                                                            {{ isset($data->product_deliverability_type) && $data->product_deliverability_type != 'null' && $data->product_deliverability_type == 'zipcode_wise_deliverability' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="muqtarin-option-card">
                                                    <div>
                                                        <label for="city_wise_deliverability_switch">
                                                            {{ labels('admin_labels.city_wise_deliverability', 'City Wise Deliverability') }}
                                                        </label>
                                                        @if ($shiprocketEnabled)
                                                            <div class="text-danger small mt-1">
                                                                {{ labels('admin_labels.shipping_method_disabled_due_to_standard_shipping', '(Disabled because standard shipping is on from shipping method)') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="radio"
                                                            id="city_wise_deliverability_switch"
                                                            name="product_deliverability"
                                                            {{ $shiprocketEnabled ? 'disabled' : '' }}
                                                            {{ isset($data->product_deliverability_type) && $data->product_deliverability_type != 'null' && $data->product_deliverability_type == 'city_wise_deliverability' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if (!$shiprocketEnabled)
                                        <!-- Delivery charge settings -->
                                        <div class="muqtarin-subpanel">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">{{ labels('admin_labels.delivery_charge_setting', 'Delivery Charge Setting') }}</h6>
                                                <a href="{{ route('admin.zones.index') }}" target="_blank"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="fa fa-plus-circle"></i>
                                                </a>
                                            </div>
                                            <input type="hidden" name="delivery_charge_type_value"
                                                value="{{ $data->delivery_charge_type ?? '' }}">
                                            <div class="row g-3">
                                                <div class="col-md-4 zipcode_wise_delivery_charge {{ ($data->product_deliverability_type ?? '') == 'city_wise_deliverability' ? 'd-none' : '' }}">
                                                    <div class="muqtarin-option-card">
                                                        <label for="zipcode_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.zipcode_wise', 'Zipcode Wise') }}
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="zipcode_wise_delivery_charge_switch"
                                                                name="delivery_charge_type"
                                                                {{ isset($data->delivery_charge_type) && $data->delivery_charge_type != 'null' && $data->delivery_charge_type == 'zipcode_wise_delivery_charge' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 city_wise_delivery_charge {{ ($data->product_deliverability_type ?? '') == 'zipcode_wise_deliverability' ? 'd-none' : '' }}">
                                                    <div class="muqtarin-option-card">
                                                        <label for="city_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.city_wise', 'City Wise') }}
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="city_wise_delivery_charge_switch"
                                                                name="delivery_charge_type"
                                                                {{ isset($data->delivery_charge_type) && $data->delivery_charge_type != 'null' && $data->delivery_charge_type == 'city_wise_delivery_charge' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="muqtarin-option-card">
                                                        <label for="product_wise_delivery_charge_switch">
                                                            {{ labels('admin_labels.product_wise', 'Product Wise') }}
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="product_wise_delivery_charge_switch"
                                                                name="delivery_charge_type"
                                                                {{ isset($data->delivery_charge_type) && $data->delivery_charge_type != 'null' && $data->delivery_charge_type == 'product_wise_delivery_charge' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="muqtarin-option-card">
                                                        <label for="global_delivery_charge_switch">
                                                            {{ labels('admin_labels.global_delivery_charge', 'Global Delivery Charge') }}
                                                        </label>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="radio"
                                                                id="global_delivery_charge_switch"
                                                                name="delivery_charge_type"
                                                                {{ isset($data->delivery_charge_type) && $data->delivery_charge_type != 'null' && $data->delivery_charge_type == 'global_delivery_charge' ? 'checked' : '' }}>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                $d_none =
                                                    ($data->delivery_charge_type ?? '') == 'product_wise_delivery_charge' ||
                                                    ($data->delivery_charge_type ?? '') == 'zipcode_wise_delivery_charge' ||
                                                    ($data->delivery_charge_type ?? '') == 'city_wise_delivery_charge'
                                                        ? 'd-none'
                                                        : '';
                                            @endphp
                                            <div class="row g-3 mt-1">
                                                <div class="col-md-6 {{ $d_none }}" id="delivery_charge_amount_field">
                                                    <label class="form-label" for="delivery_charge_amount">
                                                        {{ labels('admin_labels.delivery_charge', 'Delivery Charge') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                    </label>
                                                    <input type="number" min="0" class="form-control"
                                                        id="delivery_charge_amount"
                                                        name="delivery_charge_amount"
                                                        value="{{ $data->delivery_charge_amount ?? 0 }}">
                                                </div>
                                                <div class="col-md-6 {{ $d_none }}" id="minimum_free_delivery_amount_field">
                                                    <label class="form-label" for="minimum_free_delivery_amount">
                                                        {{ labels('admin_labels.minimum_free_delivery_amount', 'Minimum Amount for Free Delivery') }}
                                                        <span class='text-asterisks text-sm'>*</span>
                                                    </label>
                                                    <input type="number" min="0" class="form-control"
                                                        id="minimum_free_delivery_amount"
                                                        name="minimum_free_delivery_amount"
                                                        value="{{ $data->minimum_free_delivery_amount ?? 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="muqtarin-submit-wrap">
                            <button type="submit" class="btn btn-primary submit_button muqtarin-cta-btn">
                                <span>{{ labels('admin_labels.update_store', 'Update Store') }}</span>
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

            var cardObs = new MutationObserver(function() {
                syncWizardState();
            });
            cards.forEach(function(card) {
                cardObs.observe(card, { attributes: true, attributeFilter: ['class'] });
            });

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
