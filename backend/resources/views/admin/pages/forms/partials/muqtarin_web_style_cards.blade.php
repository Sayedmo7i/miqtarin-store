@php
    $fieldName = $fieldName ?? '';
    $selectClass = $selectClass ?? '';
    $selectedValue = $selectedValue ?? '';
    $options = $options ?? [];
    $columns = $columns ?? 'auto';
    $resolvedValue = $selectedValue ?: ($options[0]['value'] ?? '');
@endphp
<div class="mq-style-picker-wrap" data-mq-picker-for="{{ $fieldName }}">
    <select class="{{ $selectClass }} mq-synced-select d-none" name="{{ $fieldName }}">
        @foreach ($options as $opt)
            <option value="{{ $opt['value'] }}" {{ $resolvedValue === $opt['value'] ? 'selected' : '' }}>
                {{ $opt['label'] }}
            </option>
        @endforeach
    </select>
    <div class="mq-style-card-grid {{ $columns === 'compact' ? 'mq-style-card-grid--compact' : '' }}">
        @foreach ($options as $opt)
            @php $isActive = $resolvedValue === $opt['value']; @endphp
            <button type="button"
                class="mq-style-card {{ $isActive ? 'is-selected' : '' }} {{ !empty($opt['muqtarin']) ? 'is-muqtarin' : '' }}"
                data-mq-select-target="{{ $fieldName }}"
                data-mq-option-value="{{ $opt['value'] }}"
                aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                <div class="mq-style-card-top">
                    <span class="mq-style-card-icon">
                        <i class="bx {{ $opt['icon'] ?? 'bx-layout' }}"></i>
                    </span>
                    <span class="mq-style-tier-badge {{ !empty($opt['muqtarin']) ? 'is-muqtarin' : 'is-classic' }}">
                        {{ !empty($opt['muqtarin']) ? labels('admin_labels.muqtarin_badge', 'MUQTARIN') : labels('admin_labels.classic_badge', 'Classic') }}
                    </span>
                </div>
                <div class="mq-style-card-title">{{ $opt['label'] }}</div>
                @if (!empty($opt['hint']))
                    <div class="mq-style-card-hint">{{ $opt['hint'] }}</div>
                @endif
                <span class="mq-style-card-check" aria-hidden="true"><i class="bx bx-check"></i></span>
            </button>
        @endforeach
    </div>
</div>
