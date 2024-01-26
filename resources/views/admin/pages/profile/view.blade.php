@extends('admin.layouts.app')
@section('title', $title)

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                </i>
            </div>
            <div>{{ $header }}
                <div class="page-title-subheading">{{ $subheader }} </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-md-center">
    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ $message }}</strong>
            <button type="button" style="height:-webkit-fill-available; width: 50px;" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <x-admin.linkable-nav :menu="$menu" />
    {!! $content !!}
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        
    });
</script>
@endpush