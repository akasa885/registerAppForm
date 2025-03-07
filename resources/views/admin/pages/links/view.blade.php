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
                    className: 'text-center',
                    render: function (data, type, row) {
                        let html = "";
                        
                        html += `${data} <br/> ${row.hide_button}`;

                        return html;
                    }
                },
                {
                    targets: 1,
                    render: function (data, type, row) {
                        let html = "";
                        html += `<span class="fw-bolder">${data}</span><br/>`;
                        html += `<span class="text-muted">Quota: <span class="text-danger">`;
                        if (row.member_limit) {
                            html += `${row.member_limit} orang`;
                        } else {
                            html += `Unlimited`;
                        }
                        html += `<span class="text-primary"> | ${row.method_pay}</span></span></span><br/>`;
                        html += `<span class="text-muted" style="font-size:.85em;">Views: <span class="text-info"> ${row.viewed_count} kali</span></span><br/>`;
                        return html;
                    },
                },
                {
                    targets: 2,
                    render: function (data, type, row) {
                        let html = "";
                        html += `<span type="button" class="fw-bolder link-path" id="link-path-${row.DT_RowIndex}" onclick="copyClipboard(this);" data-toggle="tooltip" data-placement="top" title="Click to Copy!" data-original-title="Click to Copy!" data-link="${data}">${data}</span><br/>`;
                        html += `<input type="text" class="d-none" id="link-path-${row.DT_RowIndex}-input" value="${data}">`;
                        html += `<span class="text-muted">Tipe: <span class="text-danger">${row.link_type}</span></span><br/>`;
                        html += row.date_status;

                        return html;
                    },
                },
            ],
            fnDrawCallback: function( oSettings ) {
                //
            }
        });
    });

    function showHideEvent(idLink) {
        let urlApi = "{{route('admin.link.change.visibility', ':id')}}";
        urlApi = urlApi.replace(':id', idLink);
        //prevent double click
        $(`#show-hide-${idLink}`).prop('disabled', true);

        $.ajax({
            url: urlApi,
            type: "POST",
            data: {
                id: idLink
            },
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        $('#data_users_side').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Failed!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: 'Failed!',
                    text: 'Something went wrong!',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        });
    }
</script>
@endpush