@extends('admin.layouts.app')
@section('title', 'Mengedit Link')
@section('content')
<div class="container">
    <form action="{{ route('admin.attendance.store', ['type' => $type]) }}" method="POST">
        @csrf
        <input type="hidden" name="attendance_type" value="{{$type}}">
        @if ($type == 'day')
        @include('admin.pages.attendance.cDay')
        @elseif ($type == 'hourly')
        @include('admin.pages.attendance.cHourly')
        @else
        <h2> Requested Url Not Found ! </h2>
        @endif
    </form>
</div>     
@endsection

@push('scripts')
<script src="{{ asset('vendor/laravel-filemanager/js/stand-alone-button.js') }}"></script>
<script src="//cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#select-event').select2({
            placeholder: "Select Event",
            allowClear: true
        });

        // confirm-cert on change
        $('#confirm-cert').on('change', function(){
            if($(this).is(':checked')){
                $('#certificate_extended').removeClass('d-none');
            }else{
                $('#certificate_extended').addClass('d-none');
            }
        });

        // get status of confirm-cert
        var confirmCert = $('#confirm-cert').is(':checked');
        if(confirmCert){
            $('#certificate_extended').removeClass('d-none');
        }
    });
</script>
@endpush