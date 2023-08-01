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
            <div>List Member Pendaftaran
                <div class="page-title-subheading">This is an link panel that you can manage your members register
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
                    Member List : {{$title}}
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <a href="{{ route('admin.export.member-export', ['link' => $link]) }}" class="border-0 btn-transition btn btn-outline-primary">Download Data Participant</a>
                        <a href="{{ route('admin.link.view') }}" class="border-0 btn-transition  btn btn-outline-danger">Kembali</a>
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

@push('modal')
{{-- ============================= Modal  Section================================ --}}
<div class="modal fade" id="ModalViewPict" tabindex="-1" role="dialog" aria-labelledby="ModalViewPictLabel" style="display: none;" aria-hidden="true">
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
                    <input type="hidden" id="bukti_id_member" value="NaN" name="id_member">
                    <img src="{{ asset('/images/default/no-image.png') }}" id="bukti-img" class="img-fluid" alt="bukti">
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

{{-- ============================= Modal  Section================================ --}}
<div class="modal fade" id="ModalDetailPeserta" tabindex="-1" role="dialog" aria-labelledby="ModalDetailPeserta" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalDetailPeserta">Daftar Peserta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="list-of-members">
                    <input type="hidden" id="bukti_id_member" value="NaN" name="id_member">
                    <h4>Loading...</h4>
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
    $(function() {
        $('#data_users_side').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('admin.link.dtable.member', ['id' => $id])}}",
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
            columnDefs: [
                {
                    targets: 1,
                    render : function(data, type, row) {
                        let html = '';
                        html += '<div class="widget-content-left flex2">';
                        html += '<div class="widget-heading">' + data + '</div>';
                        html += '<div class="widget-subheading opacity-7" style="font-size:.85em;">Reg: ' + row.registered + '</div>';
                        html += '</div>';
                        return html;
                    }
                },
                {
                    targets: 2,
                    render : function (data, type, row) {
                        let html = '';
                        html += '<div class="widget-content-left flex2">';
                        html += '<div class="widget-heading">' + data + '</div>';
                        html += '<div class="widget-subheading opacity-7" style="font-size:.85em;">Phone: ' + row.contact_number + '</div>';
                        html += '</div>';
                        return html;
                    }
                }
            ]
        });
    });
    $(document).ready(function(){
        $('#bukti-diterima').on('click', function(){
            // $('#ModalViewPict :input[type="button"]').prop('disabled', true);
            offBuktiButton();
            let memberId = document.getElementById("bukti_id_member").value;
            if (memberId == "NaN" || memberId == "") {
                alert("Aksi ditolak, silahkan refresh halaman ini.");
                onBuktiButton();
                return;
            }
            ajaxUpdateBukti(true, memberId);
        });
        $('#bukti-ditolak').on('click', function(){
            // $('#ModalViewPict :input[type="button"]').prop('disabled', true);
            offBuktiButton();
            let memberId = document.getElementById("bukti_id_member").value;
            if (memberId == "NaN" || memberId == "") {
                alert("Aksi ditolak, silahkan refresh halaman ini.");
                onBuktiButton();
                return;
            }
            ajaxUpdateBukti(false, memberId);
        });
    });
    function ajaxUpdateBukti(received = true, memberId)
    {
        $.ajax({
            type: "post",
            url: "{{route('admin.member.up.bukti')}}",
            data: {id:memberId, received:received},
            cache: false,
            success: function(data){
                if (data.success) {
                    alert(data.message);
                    onBuktiButton();
                    location.reload();
                }else{
                    alert(data.message);
                    console.log(data.error);
                    onBuktiButton();
                }
            },
            error: function(xhr, status, error){
                if (xhr.status == 422) {
                    alert(xhr.responseJSON.message);
                }else{
                    alert("Response server error");
                }
                onBuktiButton();
            }
        })
    }
    function detailPeserta(member_id) {
        var listWrapper = document.getElementById("list-of-members");
        listWrapper.innerHTML = "<h4>Loading...</h4>";
        $.ajax({
            type: "get",
            url: "{{ route('admin.ajax.sub-member.particapant') }}",
            data: {parent_member:member_id},
            cache: false,
            success: function (res) {
                if (res.status == 'success') {
                    listWrapper.innerHTML = res.view;
                }
            },
            error: function (xhr, status, error) {
                listMemberResponse('failed to load data');
                if (xhr.status == 422) {
                    alert(xhr.responseJSON.message);
                }else{
                    alert("Response server error");
                }
            }
        })
    }
    function viewProof(buktiAsset, name = null) {
        buktiReset();
        if (name) {
            document.getElementById("nameMember").innerHTML = name;
        }
        // hide button
        document.getElementById("bukti-diterima").style.display = "none";
        document.getElementById("bukti-ditolak").style.display = "none";
        document.getElementById("bukti-img").src = buktiAsset;
    }
    function viewPayment(member_id) {
        buktiReset();
        let url = "{{route('admin.member.lihat.bukti', ['id' => ":id"])}}";
        url = url.replace(':id', member_id);
        $.ajax({
            type: "get",
            url: url,
            cache: true,
            success: function(data){
                if (data.success) {
                    document.getElementById("bukti-img").src = data.bukti;
                    document.getElementById("bukti_id_member").value = data.memberId;
                }else{
                    alert(data.message);
                }
            },
            error: function(data){
                alert('response server error');
            }
        })
    }
    function buktiReset(){
        document.getElementById("bukti-img").src = '{{ asset('/images/default/no-image.png') }}';
    }
    function listMemberResponse(text) {
        var listWrapper = document.getElementById("list-of-members");
        listWrapper.innerHTML = `<h4>${text}</h4>`;
    }
    function offBuktiButton(){
        $('#bukti-diterima').prop('disabled', true);
        $('#bukti-ditolak').prop('disabled', true);
    }
    function onBuktiButton(){
        $('#bukti-diterima').prop('disabled', false);
        $('#bukti-ditolak').prop('disabled', false);
    }
</script>
@endpush