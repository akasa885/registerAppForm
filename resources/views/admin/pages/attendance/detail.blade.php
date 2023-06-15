@extends('admin.layouts.app')
@section('title', 'Halaman List Member Pendaftaran')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                </i>
            </div>
            <div>List Absensi Member {{$event->title}}
                <div class="page-title-subheading">This is an link panel that you can manage your members attendance
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-md-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Daftar Absen
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <a href="{{ route('admin.export.member-export', ['link' => $event]) }}" class="border-0 btn-transition btn btn-outline-primary">Download Data Absensi</a>
                        <a href="{{ route('admin.link.view') }}" class="border-0 btn-transition  btn btn-outline-danger">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Sudah Absen
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data_attendances" class="mb-0 table display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="min-width:50px">#</th>
                                <th style="min-width:125px">Nama Lengkap</th>
                                <th style="min-width:150px">Informasi</th>
                                <th style="min-width:150px">Instansi</th>
                                <th style="min-width:100px">Waktu Absen</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Belum Absen
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data_attendance_yet" class="mb-0 table display" style="width:100%">
                        <thead>
                            <tr>
                                <th style="min-width:50px">#</th>
                                <th style="min-width:125px">Nama Lengkap</th>
                                <th style="min-width:150px">Informasi</th>
                                <th style="min-width:150px">Instansi</th>
                                <th style="min-width:100px">Waktu Absen</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $(function() {
        $('#data_attendances').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.attendance.dtable') }}",
                type: "GET",
                data: {
                    attendings: true,
                    attend_id: {{ $attendance->id}}
                },
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'instansi',
                    name: 'instansi'
                },
                {
                    data: 'attend',
                    name: 'attend'
                },
            ],
            columnDefs: [
                {
                    targets: 2,
                    render : function(data, type, row) {
                        // email & phone_number
                        let html = '';
                        html += '<div class="widget-content-left flex2">';
                        html += '<div class="widget-heading"> Email: ' + row.email + '</div>';
                        html += '<div class="widget-subheading opacity-7">Nomor: ' + row.phone_number + '</div>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
        });
        $('#data_attendance_yet').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.attendance.dtable') }}",
                type: "GET",
                data: {
                    attendings: false,
                    attend_id: {{ $attendance->id}}
                },
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'instansi',
                    name: 'instansi'
                },
                {
                    data: 'attend',
                    name: 'attend'
                },
            ],
            columnDefs: [
                {
                    targets: 2,
                    render : function(data, type, row) {
                        // email & phone_number
                        let html = '';
                        html += '<div class="widget-content-left flex2">';
                        html += '<div class="widget-heading"> Email: ' + row.email + '</div>';
                        html += '<div class="widget-subheading opacity-7">Nomor: ' + row.phone_number + '</div>';
                        html += '</div>';
                        return html;
                    }
                }
            ],
        });
    });
</script>
@endpush