<div class="header @@classList bg-white">

    @php
        use App\Models\Store;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\Support\Str;
        use App\Services\TranslationService;
        use App\Services\StoreService;
        use App\Services\MediaService;
        use App\Services\SettingService;
        $language_code = app(TranslationService::class)->getLanguageCode();
        $user = Auth::user();
        $settings = app(SettingService::class)->getSettings('admin_preference', true);

        // Decode if JSON string
        if (is_string($settings)) {
            $settings = json_decode($settings);
        }
        $isPublicDisk = $user->disk == 'public' ? 1 : 0;
        $user_image = $isPublicDisk
            ? (!empty($user->image) && file_exists(public_path(config('constants.USER_IMG_PATH') . $user->image))
                ? app(MediaService::class)->getMediaImageUrl($user->image, 'USER_IMG_PATH')
                : app(MediaService::class)->getImageUrl('no-user-img.jpeg', '', '', 'image', 'NO_USER_IMAGE'))
            : $user->image;

        $store_details = fetchDetails(Store::class, ['status' => 1], '*');
        $store_count = count($store_details);

        $default_store_id = '';
        $stores = Store::where('is_default_store', 1)->where('status', 1)->get();
        if ($stores->isNotEmpty()) {
            $default_store_id = $stores[0]->id;
            $default_store_name = app(TranslationService::class)->getDynamicTranslation(
                Store::class,
                'name',
                $stores[0]->id,
                $language_code,
            );
            $isPublicDisk = $stores[0]->disk == 'public' ? 1 : 0;
            $default_store_image = $stores[0]->image ?? '';
        } else {
            $default_store_id = '';
            $default_store_name = '';
            $default_store_image = '';
        }

        if (session('store_id') !== null && !empty(session('store_id'))) {
            $store_id = session('store_id');
        } else {
            $store_id = $default_store_id;
            session(['store_id' => $default_store_id]);
            session(['store_name' => $default_store_name]);
            session(['store_image' => $default_store_image]);
        }

        $store_name = session('store_name') !== null && !empty(session('store_name')) ? session('store_name') : '';
        if (!empty($stores) && isset($stores[0])) {
            $isPublicDisk = $stores[0]->disk == 'public' ? 1 : 0;
        } else {
            $isPublicDisk = 0;
        }

        $rawStoreImg = session('store_image') ?: $default_store_image;
        $store_image = !empty($rawStoreImg) ? $rawStoreImg : '';

        use App\Models\Language;

        $languages = Language::all();

    @endphp
    <nav class="navbar navbar-main navbar-expand-lg px-0 shadow-none border-radius-xl" id="navbarBlur" data-scroll="false">
        <div id="app_url" data-app-url="{{ rtrim(url('/'), '/') . '/' }}"></div>
        <div class="align-items-center d-flex px-3 px-md-4 py-1 w-100 muqtarin-topbar-inner">
            <button type="button" class="muqtarin-icon-btn" id="muqtarin-sidebar-toggle"
                onclick="muqtarinToggleSidebar()" title="Toggle Sidebar" aria-label="Toggle Sidebar">
                <i class='bx bx-menu'></i>
            </button>

            <div class="navbar-collapse justify-content-start flex-grow-1 muqtarin-topbar-store" id="navbar">
                @if ($store_count > 1 && optional($settings)->store_mode != 'single')
                    <ul class="navbar-nav w-100 mb-0">
                        <li class="nav-item dropdown d-flex align-items-center dropdown-store">
                            <div class="module-dropdown-box">
                                <a class="nav-link dropdown-toggle hide-arrow d-flex align-items-center muqtarin-store-trigger" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <img src="{{ app(MediaService::class)->getMediaImageUrl($store_image, 'STORE_IMG_PATH') }}"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                                        alt="" class="avatar rounded-circle avatar-sm flex-shrink-0">
                                    <span class="ms-2 header_store_name">{{ app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store_id, $language_code) }}</span>
                                    <i class="fas fa-angle-down ms-2 flex-shrink-0"></i>
                                </a>

                                <div class="moduleDropDown">
                                    <ul class="dropdown-menu stores_dropdown" data-bs-popper="none"
                                        data-bs-placement="bottom-start" id="store-dropdown">
                                        <div class="row ms-2">
                                            <p class="col-12 text-bold">{{ labels('admin_labels.select_store', 'Select Store') }}</p>
                                        </div>
                                        <div class="d-flex flex-row">
                                            @forelse($store_details as $store)
                                                <li class="dropdown-item" data-store-id="{{ $store->id }}"
                                                    data-store-name="{{ app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store->id, $language_code) }}"
                                                    data-store-image="{{ $store->image }}">
                                                    <div class="align-items-center">
                                                        <div>
                                                            <img alt=""
                                                                src="{{ route('admin.dynamic_image', [
                                                                    'url' => app(MediaService::class)->getMediaImageUrl($store->image, 'STORE_IMG_PATH'),
                                                                    'width' => 110,
                                                                    'quality' => 90,
                                                                ]) }}"
                                                                onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                                                                class="">
                                                        </div>
                                                        <span
                                                            class="fw-semibold d-block p-2 text-center">{{ Str::limit(app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store->id, $language_code), 10, '...') }}
                                                        </span>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="dropdown-item disabled d-flex justify-content-center">
                                                    <div class="col-6 text-center">No Stores found.</div>
                                                </li>
                                                <div class="d-flex justify-content-center">
                                                    <i class='bx bxs-plus-circle'></i><a href="/admin/store"
                                                        target="_blank">add a store</a>
                                                </div>
                                            @endforelse
                                        </div>
                                    </ul>
                                </div>
                            </div>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav w-100 mb-0">
                        <li class="nav-item dropdown d-flex align-items-center dropdown-store muqtarin-store-trigger">
                            <img src="{{ app(MediaService::class)->getMediaImageUrl($store_image, 'STORE_IMG_PATH') }}"
                                onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                                alt="" class="avatar rounded-circle avatar-sm flex-shrink-0">
                            <span class="ms-2 header_store_name">{{ app(TranslationService::class)->getDynamicTranslation(Store::class, 'name', $store_id, $language_code) }}</span>
                        </li>
                    </ul>
                @endif
            </div>

            @php
                $language_code = session()->get('locale') ?? 'en';
                $selected_language = fetchDetails(Language::class, ['code' => $language_code], 'language');
                $selected_language = !$selected_language->isEmpty() ? $selected_language[0]->language : 'English';
            @endphp

            <div class="muqtarin-topbar-actions">
                @if (!empty($selected_language))
                    <label for="" class="badge bg-primary muqtarin-lang-pill mb-0">{{ $selected_language }}</label>
                @endif

                <button type="button" class="muqtarin-icon-btn" onclick="muqtarinToggleTheme()"
                    title="Toggle Dark / Light Mode" aria-label="Toggle Dark / Light Mode">
                    <i class="bx bx-moon" id="muqtarin-theme-icon"></i>
                </button>

                <div class="nav-item dropdown d-flex justify-content-center notifiationDropDown">
                    <a href="javascript:;" class="muqtarin-icon-btn p-0" id="languageDropdown" data-bs-toggle="dropdown"
                        aria-expanded="false" title="Language">
                        <i class="bx bx-globe"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end px-2 py-2" aria-labelledby="languageDropdown">
                        @foreach ($languages as $language)
                            <li>
                                <a class="dropdown-item changeLang" data-lang-code="{{ $language->code }}">
                                    {{ ucwords($language->language) }} - {{ strtoupper($language->code) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="nav-item dropdown d-flex justify-content-center notifiationDropDown d-none">
                    <a href="javascript:;" class="nav-link text-white p-0" id="notificationMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bx bx-bell'></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4"
                        aria-labelledby="notificationMenuButton">
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="my-auto">
                                        <img src="" class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">New message</span> from Laur
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="fa fa-clock me-1"></i>
                                            13 minutes ago
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="profileDropDown" class="dropdown d-flex align-items-center">
                    <a href="javascript:;" class="nav-link d-flex align-items-center gap-2 p-0"
                       id="profileMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <img class="avatar rounded-circle avatar-sm flex-shrink-0"
                            src="{{ app(MediaService::class)->getMediaImageUrl($user_image) }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/brand/icon.jpg') }}';"
                            alt="{{ $user->username }}">
                        <span class="nav-link-text">{{ $user->username }}</span>
                        <i class="fas fa-angle-down d-none d-sm-inline-block"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="profileMenuButton">
                        <li>
                            <a class="dropdown-item" href="/admin/account/{{ auth()->user()->id }}">
                                <i class='bx bx-user-circle'></i>
                                <span>{{ labels('admin_labels.profile', 'Profile') }}</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}">
                                <i class='bx bx-log-in-circle'></i>
                                <span>{{ labels('admin_labels.logout', 'Logout') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    @php
        $store_id = app(StoreService::class)->getStoreId();

        $store_details = fetchDetails(
            Store::class,
            ['id' => $store_id],
            ['primary_color', 'secondary_color', 'hover_color', 'active_color'],
        );
        $primary_colour =
            isset($store_details[0]->primary_color) && !empty($store_details[0]->primary_color)
                ? $store_details[0]->primary_color
                : '#0F4C3A';
        $secondary_color =
            isset($store_details[0]->secondary_color) && !empty($store_details[0]->secondary_color)
                ? $store_details[0]->secondary_color
                : '#1F2937';
        $hover_color =
            isset($store_details[0]->hover_color) && !empty($store_details[0]->hover_color)
                ? $store_details[0]->hover_color
                : '#0A3528';
        $active_color =
            isset($store_details[0]->active_color) && !empty($store_details[0]->active_color)
                ? $store_details[0]->active_color
                : '#06231A';
        $background_opacity_color = $primary_colour . '10';
    @endphp

    <style>
        :root {
            --primary-theme-color: <?=$primary_colour ?>;
            --background_opacity_color: <?=$background_opacity_color ?>;
            --secondary-theme-color: <?=$secondary_color ?>;
            --hover-color: <?=$hover_color ?>;
            --active-color: <?=$active_color ?>;
            /* MUQTARIN brand tokens (from DESIGN.md) */
            --pine: <?=$primary_colour ?>;
            --pine-hover: <?=$hover_color ?>;
        }
    </style>

</div>
