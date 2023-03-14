@extends('admin.layouts.app')
@section('title', 'Mengedit Link')
@section('content')
<div class="container">
    <form action="{{ route('admin.link.update', ['id' => $link_detail->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="event_type" value="{{$type_reg}}">
        @if ($type_reg == 'pay')
        @include('admin.pages.links.edit_pay')
        @elseif ($type_reg == 'free')
        @include('admin.pages.links.edit_free')
        @else
        <h2> Requested Url Not Found ! </h2>
        @endif
    </form>
</div>     
@endsection

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script src="//cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#datepicker-1').datepicker({
            format: 'dd-mm-yyyy'
        });
        $('#datepicker-2').datepicker({
            format: 'dd-mm-yyyy'
        });
    });
    var options = {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
    };

    @if ($type_reg == 'pay')
        CKEDITOR.replace('my-editor-1', options);
        CKEDITOR.replace('my-editor-2', options);
        CKEDITOR.replace('my-editor-3', options);
    @else
        CKEDITOR.replace('my-editor-1', options);
    @endif
</script>
@endpush