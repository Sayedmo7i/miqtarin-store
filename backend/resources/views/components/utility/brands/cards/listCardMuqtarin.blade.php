@props(['brands', 'language_code'])
@php
    use App\Models\Brand;
    use App\Services\TranslationService;
    use App\Services\MediaService;
@endphp
<div class="mq-container">
    @if (count($brands) >= 1)
        <div class="mq-brands-grid">
            @foreach ($brands as $brand)
                @php
                    $brand_img = app(MediaService::class)->dynamic_image($brand->image, 400);
                    $brand_name = app(TranslationService::class)->getDynamicTranslation(Brand::class, 'name', $brand->id, $language_code);
                @endphp
                <a wire:navigate href="{{ customUrl('products/?brand=' . $brand->slug) }}"
                    class="mq-brand-card slider-link"
                    data-link="{{ customUrl('products/?brand=' . $brand->slug) }}">
                    <div class="mq-brand-logo-box">
                        <img class="blur-up lazyload" data-src="{{ $brand_img }}" src="{{ $brand_img }}"
                            alt="{{ $brand_name }}" title="{{ $brand_name }}" />
                    </div>
                    <h4 class="mq-brand-name">{{ $brand_name }}</h4>
                </a>
            @endforeach
        </div>
    @endif
</div>
