@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.no_data_found', 'No Data Found') }}
@endsection
@section('content')
    <div class="container-fluid py-5">
        <div class="card mx-auto" style="max-width: 520px;">
            <div class="card-body text-center py-5 px-4">
                <div class="muqtarin-empty-state">
                    <i class="bx bx-folder-open" style="font-size: 3rem;"></i>
                    <h4 class="mt-3 mb-2">{{ labels('admin_labels.no_data_found', 'No Data Found') }}</h4>
                    <p class="text-muted mb-4">
                        {{ labels('admin_labels.no_data_found_text', 'The requested record could not be found or has no matching items.') }}
                    </p>
                    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('admin.home') }}"
                        class="btn btn-primary">
                        <i class="bx bx-arrow-back me-1"></i>
                        {{ labels('admin_labels.back', 'Back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
