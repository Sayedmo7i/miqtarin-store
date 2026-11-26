@props(['categories', 'language_code'])
@php
    use App\Models\Category;
    use App\Services\TranslationService;
    use App\Services\MediaService;
@endphp
<div class="mq-container">
    @if (($categories['countRes'] ?? 0) >= 1)
        <div class="mq-cat-bento-grid">
            @foreach ($categories['categories'] as $category)
                @php
                    $cat_name = app(TranslationService::class)->getDynamicTranslation(Category::class, 'name', $category->id, $language_code);
                    $cat_img = app(MediaService::class)->dynamic_image($category->banner ?: $category->image, 650);
                @endphp
                <a wire:navigate href="{{ customUrl('categories/' . $category->slug . '/products') }}"
                    class="mq-cat-bento-card slider-link"
                    data-link="{{ customUrl('categories/' . $category->slug . '/products') }}">
                    <div class="mq-cat-bento-media">
                        <img class="blur-up lazyload" data-src="{{ $cat_img }}" src="{{ $cat_img }}"
                            alt="{{ $cat_name }}" title="{{ $cat_name }}" />
                    </div>
                    <div class="mq-cat-bento-body">
                        <div>
                            <h3>{{ $cat_name }}</h3>
                            @if (isset($category->product_count))
                                <span class="small" style="color: var(--mq-muted);">{{ $category->product_count }} {{ labels('front_messages.products', 'Products') }}</span>
                            @endif
                        </div>
                        <span class="mq-cat-bento-arrow"><i class="bx bx-chevron-right fs-5"></i></span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
