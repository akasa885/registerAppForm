@extends('admin.layouts.app')
@section('title', 'Halaman Trash Member Pendaftaran')

<x-sweet-alert />

@section('content')
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                    </i>
                </div>
                <div>List Trash Member Pendaftaran
                    <div class="page-title-subheading">This is an link panel that you can manage your trash members register
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-center">
        <x-admin.linkable-nav :menu="$menu" />
        <div class="col-md-11 col-lg-11">
            <div class="mb-3 card">
                <div class="card-header-tab card-header-tab-animation card-header">
                    <div class="card-header-title" style="white-space:normal">
                        <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                        Member List : {{ $title }}
                    </div>
                    <div class="btn-actions-pane-right">
                        <div class="nav">
                            <a href="{{ route('admin.link.view') }}"
                                class="border-0 btn-transition  btn btn-outline-danger">Kembali</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="data_users_side" class="mb-0 table display" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="min-width:50px">#</th>
                                    <th style="width: 30%">Nama Lengkap</th>
                                    <th style="width: 20%">Email</th>
                                    <th style="width: 20%">Instansi</th>
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

@push('modal')
    {{-- ============================= Modal  Section================================ --}}
    <div class="modal fade" id="ModalInfoMember" tabindex="-1" role="dialog" aria-labelledby="ModalInfoMemberLabel"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalInfoMemberLabel">Info Member</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        $(function() {
            $('#data_users_side').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.link.dtable.member.trash', ['link' => $id]) }}",
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
                        data: 'corporation',
                        name: 'instansi'
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
                columnDefs: [{
                        targets: 1,
                        render: function(data, type, row) {
                            let html = '';
                            html += '<div class="widget-content-left flex2">';
                            html += '<div class="widget-heading">' + data + '</div>';
                            html +=
                                '<div class="widget-subheading opacity-7" style="font-size:.85em;">Deleted: ' +
                                row.registered + '</div>';
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        targets: 2,
                        render: function(data, type, row) {
                            let html = '';
                            html += '<div class="widget-content-left flex2">';
                            html += '<div class="widget-heading">' + data + '</div>';
                            html +=
                                '<div class="widget-subheading opacity-7" style="font-size:.85em;">Phone: ' +
                                row.contact_number + '</div>';
                            html += '</div>';
                            return html;
                        }
                    }
                ]
            });
        });

        const pageTrashMemberLink = (function () {

            return {
                init: () => {},
                info: (memberId) => {
                    let url = `{{ route('admin.ajax.member.info', ['memberId' => ':id']) }}`;
                    let modalContent = $('#ModalInfoMember');
                    let modalBody = modalContent.find('.modal-body');
                    modalContent.modal('show');

                    url = url.replace(':id', memberId);
                    $.ajax({
                        url: url + '?type=trash',
                        type: 'GET',
                        beforeSend: function() {
                            modalBody.html('<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');
                        },
                        success: function(response) {
                            if (response.success) {
                                modalBody.html(response.view);
                            } else {
                                modalBody.html(response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            modalBody.html('<span class="text-danger">Terjadi kesalahan saat memuat data</span>');
                        }
                    });
                },
            }
        })();
    </script>
@endpush
