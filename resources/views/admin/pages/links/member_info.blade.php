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
                <div>List Penanya
                    <div class="page-title-subheading">This is an link panel that you can manage your questioner
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-md-center">
        <div class="col-md-11 col-lg-11">
            <div class="mb-3 card">
                <div class="card-header-tab card-header-tab-animation card-header">
                    <div class="card-header-title">
                        <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                        List Penanya : {{ $title }}
                    </div>
                    <div class="btn-actions-pane-right">
                        <div class="nav">
                            <a href="{{ route('admin.export.member-export', ['link' => $link]) }}"
                                class="border-0 btn-transition btn btn-outline-primary">Download Data Participant</a>
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
                                    <th style="width: 30%">Nama Lengkap</th>
                                    <th style="width: 20%">Instansi</th>
                                    <th style="width: 20%">Pertanyaan 1</th>
                                    <th style="width: 10%">Pertanyaan 2</th>
                                    <th style="width: 20%">Pertanyaan 3</th>
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
    <div class="modal fade" id="ModalViewPict" tabindex="-1" role="dialog" aria-labelledby="ModalViewPictLabel"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalViewPictLabel">Bukti Terupload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="pict-payment">
                        <input type="hidden" id="bukti_id_member" value="" name="id_member">
                        <img src="{{ asset('/images/default/no-image.png') }}" id="bukti-img" class="img-fluid"
                            alt="bukti">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="bukti-diterima" class="btn btn-outline-success">Bukti Diterima</button>
                    <button type="button" id="bukti-ditolak" class="btn btn-outline-danger">Bukti Salah</button>
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
        $(function() {
            $('#data_users_side').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [3, 'desc']
                ],
                ajax: "{{ route('admin.link.dtable.member', ['id' => $id]) }}",
                columns: [{
                        data: 'full_name',
                        name: 'nama lengkap'
                    },
                    {
                        data: 'corporation',
                        name: 'instansi'
                    },
                    {
                        data: 'question1',
                        name: 'pertanyaan 1'
                    },
                    {
                        data: 'question2',
                        name: 'pertanyaan 2'
                    },
                    {
                        data: 'question3',
                        name: 'pertanyaan 3'
                    }
                ]
            });
        });
        $(document).ready(function() {
            $('#bukti-diterima').on('click', function() {
                // $('#ModalViewPict :input[type="button"]').prop('disabled', true);
                offBuktiButton();
                let memberId = document.getElementById("bukti_id_member").value;
                ajaxUpdateBukti(true, memberId);
            });
            $('#bukti-ditolak').on('click', function() {
                // $('#ModalViewPict :input[type="button"]').prop('disabled', true);
                offBuktiButton();
                let memberId = document.getElementById("bukti_id_member").value;
                ajaxUpdateBukti(false, memberId);
            });
        });

        function ajaxUpdateBukti(received = true, memberId) {
            $.ajax({
                type: "post",
                url: "{{ route('admin.member.up.bukti') }}",
                data: {
                    id: memberId,
                    received: received
                },
                cache: false,
                success: function(data) {
                    if (data.success) {
                        alert(data.message);
                        onBuktiButton();
                        location.reload();
                    } else {
                        alert(data.message);
                        console.log(data.error);
                        onBuktiButton();
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.status == 422) {
                        alert(xhr.responseJSON.message);
                    } else {
                        alert("Response server error");
                    }
                    onBuktiButton();
                }
            })
        }

        function viewPayment(member_id) {
            buktiReset();
            let url = "{{ route('admin.member.lihat.bukti', ['id' => ':id']) }}";
            url = url.replace(':id', member_id);
            $.ajax({
                type: "get",
                url: url,
                cache: true,
                success: function(data) {
                    if (data.success) {
                        document.getElementById("bukti-img").src = data.bukti;
                        document.getElementById("bukti_id_member").value = data.memberId;
                    } else {
                        alert(data.message);
                    }
                },
                error: function(data) {
                    alert('response server error');
                }
            })
        }

        function buktiReset() {
            document.getElementById("bukti-img").src = '{{ asset('/images/default/no-image.png') }}';
        }

        function offBuktiButton() {
            $('#bukti-diterima').prop('disabled', true);
            $('#bukti-ditolak').prop('disabled', true);
        }

        function onBuktiButton() {
            $('#bukti-diterima').prop('disabled', false);
            $('#bukti-ditolak').prop('disabled', false);
        }
    </script>
@endpush
