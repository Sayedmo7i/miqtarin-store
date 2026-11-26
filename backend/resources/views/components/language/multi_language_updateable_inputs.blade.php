@foreach($languages as $lang)
    @if($lang->code !== 'en')
        @php
            $translatedValue = '';
            if (isset($data)) {
                $decodedData = json_decode($data);
                if (is_object($decodedData) && property_exists($decodedData, $lang->code)) {
                    $translatedValue = $decodedData->{$lang->code};
                }
            }
        @endphp

        @php
            $tabDir = (($lang->is_rtl ?? 0) == 1 || in_array($lang->code, ['ar', 'he', 'fa', 'ur'], true)) ? 'rtl' : 'ltr';
        @endphp
        <div class="tab-pane fade" id="content-{{ $lang->code }}" role="tabpanel" aria-labelledby="tab-{{ $lang->code }}" dir="{{ $tabDir }}">
            <div class="mb-3">
                <label for="translated_name_{{ $lang->code }}" class="form-label">
                    {{ labels($nameKey, $nameValue) }} ({{ $lang->native_language ?: $lang->language }})
                </label>
                <input type="text" class="form-control"
                       id="translated_name_{{ $lang->code }}"
                       name="{{ $inputName }}[{{ $lang->code }}]"
                       dir="{{ $tabDir }}"
                       value="{{ $translatedValue }}">
            </div>
        </div>
    @endif
@endforeach