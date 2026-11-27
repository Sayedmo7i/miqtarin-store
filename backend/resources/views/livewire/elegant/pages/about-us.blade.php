@php
    $title = labels('front_messages.about_us', 'About Us');
@endphp
<div>
    <x-utility.breadcrumbs.breadcrumbOne :breadcrumb="$title" />
    {{-- @dd($about_us) --}}
    <div id="page-content" class="destination-section section pt-0">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-12 col-sm-12 col-md-4">
                    <div class="about-images mb-4 mb-md-0">
                        <div class="row g-3">
                            <img class="rounded-4 shadow-sm w-100 object-fit-cover"
                                src="{{ asset('storage/media/hero-editorial-1.jpg') }}"
                                onerror="this.onerror=null;this.src='{{ asset('assets/brand/logo.jpg') }}';"
                                alt="{{ labels('front_messages.about_us', 'About Us') }}"
                                style="max-height: 440px;" />
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-8">
                    <div class="about-details px-50 py-5">
                        <h2 class="fs-4 mb-4">{{ labels('front_messages.about_us', 'About Us') }}</h2>
                        <p>{!! nl2br($about_us) !!}</p>
                        <a href="/products" wire:navigate
                            class="btn btn-lg mt-md-2">{{ labels('front_messages.explore_now', 'Explore Now') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="service-section section section-color-light">
        <x-utility.others.serviceSection />
    </div>
</div>
