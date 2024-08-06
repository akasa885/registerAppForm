@extends('admin.layouts.app')
@section('title', $title)

<x-sweet-alert />

@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                    </i>
                </div>
                <div>Daftar Member
                    <div class="page-title-subheading">{{ $subtitle }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-center">
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" style="height:-webkit-fill-available; width: 50px;" class="btn-close"
                    data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="col-md-11 col-lg-11">
            <div class="mb-3 card">
                <div class="card-header-tab card-header-tab-animation card-header">
                    <div class="card-header-title">
                        <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                        Member List
                    </div>
                    <div class="btn-actions-pane-right">
                        <div class="nav">
                            <!--empty-->
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data_member_list_side" class="mb-0 table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 15px">No</th>
                                    <th style="width: 30%">Nama</th>
                                    <th style="width: 27%">Email</th>
                                    <th style="width: 13%">Terdaftar</th>
                                    <th class="d-none" style="width: 15%">Domisili</th>
                                    <th style="width: 20%">Options</th>
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

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(function() {
            $('#data_member_list_side').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.member.dt.data.member') }}",
                columns: [{
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
                        data: 'registered_count',
                        name: 'registered_count'
                    },
                    {
                        data: 'domisili',
                        name: 'domisili'
                    },
                    {
                        data: 'options',
                        name: 'Options'
                    }
                ],
                columnDefs: [{
                        targets: 0,
                        className: 'text-center'
                    },
                    {
                        targets: 1,
                        render: function(data, type, row) {
                            let html = "";
                            html += `<span class="fw-bolder">${data}</span><br/>`;
                            html += `<span class="text-muted">Phone: <span class="text-danger">`;
                            html += row.contact_number ? row.contact_number : "N/A";
                            html += `</span></span>`;

                            return html;
                        },
                    },
                    {
                        targets: 2,
                        render: function(data, type, row) {
                            let html = "";
                            html += `<span class="fw-bolder">${data}</span><br/>`;
                            html += `<span class="text-muted">Domisili: <span class="text-danger">`;
                            html += row.domisili ? row.domisili : "N/A";
                            html += `</span></span>`;

                            return html;
                        },
                    },
                    {
                        targets: 3,
                        className: 'text-center',
                        render: function(data, type, row) {
                            let html = "";
                            html += `${data} Acara`;

                            return html;
                        },
                    },
                    {
                        targets: 4,
                        className: 'text-center',
                        searchable: true,
                        sortable: false,
                        visible: false
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    //
                }
            });
        });
    </script>
@endpush
