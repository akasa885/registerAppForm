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
                        <a href="{{ route('admin.export.attendance-export', ['attendance' => $attendance]) }}" class="border-0 btn-transition btn btn-outline-primary">Download Data Absensi</a>
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
                                <th style="min-width:100px">Options</th>
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
                                <th style="min-width:100px">Options</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modal')
{{-- ============================= Modal  Section================================ --}}
<div class="modal fade" id="ModalViewPict" tabindex="-1" role="dialog" aria-labelledby="ModalViewPictLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalViewPictLabel">Bukti Terupload: 
                    <br> <span id="nameMember" style="font-size: .75em; font-weight: 700;"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pict-payment">
                    <input type="hidden" id="bukti_id_member" value="" name="id_member">
                    <img src="{{ asset('/images/default/no-image.png') }}" id="bukti-img" class="img-fluid" height="50px" alt="bukti">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    function viewProof(buktiAsset, name) {
        buktiReset();
        document.getElementById("nameMember").innerHTML = name;
        document.getElementById("bukti-img").src = buktiAsset;
    }
    function buktiReset(){
        document.getElementById("bukti-img").src = '{{ asset('/images/default/no-image.png') }}';
    }
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
                    data: null,
                    name: 'options'
                },
            ],
            columnDefs: [
                {
                    targets: 1,
                    render : function(data, type, row) {
                        let html = '';
                        html += '<div class="widget-content-left flex2">';
                        html += '<div class="widget-heading">' + data + '</div>';
                        html += '<div class="widget-subheading opacity-7">Sertifikat:';
                            if (row.certificate) {
                                html += '<span class="badge badge-success ml-2">Yes</span>';
                            } else {
                                html += '<span class="badge badge-danger ml-2">No</span>';
                            }
                        html += '</div>';
                        html += '<div class="widget-subheading opacity-7">' + row.attend + '</div>';
                        html += '</div>';
                        return html;
                    }
                },
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
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: "text-end",
                    render: function (data, type, row) {
                        let html = '';
                        if (row.options) {
                            html += `<a href="javascript:void(0)"; onClick="${row.options}" aria-expanded="false" data-toggle="modal" data-target="#ModalViewPict" class="mb-2 mr-2 badge badge-pill badge-info" style="margin-right:0.2rem;">
                                    <span class="btn-icon-wrapper pr-2 opacity-7">
                                        <i class="pe-7s-rocket fa-w-20"></i>
                                    </span>
                                    Bukti Bayar</a>`;
                        }

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