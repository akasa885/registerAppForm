@extends('admin.layouts.app')
@section('title', 'Halaman Pengelola Link')

<x-sweet-alert />

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                </i>
            </div>
            <div>Absensi Acara
                <div class="page-title-subheading">This is an attend panel that you can manage your attendances
                </div>
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
    <div class="col-md-11 col-lg-11">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Sesi Absensi
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <div class="dropdown d-inline-block">
                            <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle btn btn-outline-success">Add Sesi Absensi</button>
                            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu">
                                <a href="{{route('admin.attendance.create', ['type' => 'day'])}}" tabindex="0" class="dropdown-item">Absensi Full Day</a>
                                <a href="{{route('admin.attendance.create', ['type' => 'hourly'])}}" tabindex="0" class="dropdown-item">Absensi Dalam Jam</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="data_attend_side" class="mb-0 table display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="min-width: 150px;">Title</th>
                            <th style="min-width: 175px;">Link</th>
                            <th style="min-width: 100px;">Peserta</th>
                            <th style="min-width: 100px;">Kehadiran</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 125px;">Options</th>
                        </tr>
                    </thead>
                </table>
            </div>
            </div>
        </div>
    </div>
</div>
@endsection

<x-admin.delete-js-alerted />
<x-admin.clipboard-script />

@push('scripts')
<script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $(function() {
        $('#data_attend_side').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.attendance.dt.attendance') }}",
            columns: [{
                    data: 'link.title',
                    name: 'title'
                },
                {
                    data: 'attend_path',
                    name: 'link'
                },
                {
                    data: 'members_count',
                    name: 'pendaftar'
                },
                {
                    data: 'attend_count',
                    name: 'hadir'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'options',
                    name: 'Options'
                }
            ],
            columnDefs: [
                {
                    targets: 1,
                    render: function (data, type, row) {
                        let html = "";
                        html += `<span type="button" class="fw-bolder attend-path" id="attend-path-${row.DT_RowIndex}" onclick="copyClipboard(this);" data-toggle="tooltip" data-placement="top" title="Click to Copy!" data-original-title="Click to Copy!" data-link="${data}" style="font-size:.9em;">${data}</span><br/>`;
                        html += `<input type="text" class="d-none" id="attend-path-${row.DT_RowIndex}-input" value="${data}">`;

                        return html;
                    }
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        return `<span class="badge badge-primary">${data} Orang</span>`;
                    }
                },
                {
                    targets: 3,
                    render: function (data, type, row) {
                        return `<span class="badge badge-primary">${data} Orang</span>`;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        let html = "";
                        html += `<span class="fw-bolder">${data}</span>`;
                        return html;
                    },
                },
            ],
        });
    });
</script>
@endpush