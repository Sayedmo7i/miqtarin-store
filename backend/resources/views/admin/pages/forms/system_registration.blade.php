@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.settings', 'Settings') }}
@endsection
@section('content')
<script>window.location.href = "{{ route('admin.settings') }}";</script>
<div class="col-md-12 p-4">
    <div class="alert alert-success">
        {{ labels('admin_labels.system_registered_successfully', 'System Registered Successfully') }}
    </div>
</div>
@endsection
