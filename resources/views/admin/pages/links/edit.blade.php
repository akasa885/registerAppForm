@extends('admin.layouts.app')
@section('title', 'Mengedit Link')
@section('content')
<div class="container">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
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
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
<script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#lfm').filemanager('image');
        $('#datepicker-1').datepicker({
            format: 'dd-mm-yyyy'
        });
        $('#datepicker-2').datepicker({
            format: 'dd-mm-yyyy'
        });
    });
    var options = {
        cloudServices_tokenUrl: '',
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '{{ route("admin.ajax.ck-upload-image")."?"}}' + type_image,
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='+ '{{ csrf_token() }}',
        removePlugins: 'easyimage',
        extraPlugins: 'simplebutton, image, justify',

    };

    @if ($type_reg == 'pay')
        CKEDITOR.replace('my-editor-1', options);
        CKEDITOR.replace('my-editor-2', options);
        CKEDITOR.replace('my-editor-3', options);
        CKEDITOR.replace('my-editor-4', options);
    @else
        CKEDITOR.replace('my-editor-1', options);
        CKEDITOR.replace('my-editor-4', options);
    @endif
</script>
@endpush