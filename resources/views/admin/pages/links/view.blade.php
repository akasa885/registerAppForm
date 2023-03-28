@extends('admin.layouts.app')
@section('title', 'Halaman Pengelola Link')

@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                </i>
            </div>
            <div>List Link Pendaftaran
                <div class="page-title-subheading">This is an link panel that you can manage your links
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
                    Link List
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <div class="dropdown d-inline-block">
                            <button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle btn btn-outline-success">Add Link Pendaftaran</button>
                            <div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu">
                                <a href="{{route('admin.link.create')}}" tabindex="0" class="dropdown-item">Pendaftaran berbayar</a>
                                <a href="{{route('admin.link.create.free')}}" tabindex="0" class="dropdown-item">Pendaftaran gratis</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="data_users_side" class="mb-0 table display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 15px">No</th>
                            <th style="width: 30%">Title</th>
                            <th style="width: 27%">Link</th>
                            <th style="width: 10%">Pendaftar</th>
                            <th style="width: 10%">Status</th>
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

@push('scripts')
<script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    $(function() {
        $('#data_users_side').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('admin.link.dtable')}}",
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'link_path',
                    name: 'link'
                },
                {
                    data: 'members_count',
                    name: 'pendaftar'
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
                    targets: 0,
                    className: 'text-center'
                },
                {
                    targets: 4,
                    className: 'text-center'
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        let html = "";
                        html += `<span class="fw-bolder">${data}</span><br/>`;
                        html += `<span class="text-muted">Tipe: <span class="text-danger">${row.link_type}</span></span><br/>`;
                        html += row.date_status;
                        return html;
                    },
                },
            ],
        });
    });
</script>
@endpush