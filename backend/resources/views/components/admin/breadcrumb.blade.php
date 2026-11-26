<div class="page-header-area d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    {{-- Page title & subtitle --}}
    <div class="page-info-title">
        <h3>{{ $title }}</h3>
        @if ($subtitle)
            <p class="sub_title">{{ $subtitle }}</p>
        @endif
    </div>

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <i class='bx bx-home-smile me-1'></i>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
            </li>
            @foreach ($breadcrumbs as $crumb)
                <li class="breadcrumb-item {{ $loop->last ? 'active' : 'second_breadcrumb_item' }}"
                    @if ($loop->last) aria-current="page" @endif>
                    @if (!$loop->last && isset($crumb['url']))
                        <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                    @else
                        {{ $crumb['label'] }}
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
