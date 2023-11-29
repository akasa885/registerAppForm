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
    <x-admin.linkable-nav :menu="$menu" />
    {!! $content !!}
</div>
@endsection