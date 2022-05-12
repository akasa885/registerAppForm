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
                        <a href="{{ route('admin.link.view') }}" class="border-0 btn-transition  btn btn-outline-danger">Kembali</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="data_users_side" class="mb-0 table display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 30%">Nama Lengkap</th>
                            <th style="width: 20%">Email</th>
                            <th style="width: 20%">Instansi</th>
                            <th style="width: 10%">Status</th>
                            <th style="width: 20%">Options</th>
                        </tr>
                    </thead>
                    {{-- <tbody>
                        @foreach ($member as $item)
                            <tr>
                                <td>{{$item->full_name}}</td>
                                <td>{{$item->email}}</td>
                                <td>{{$item->corporation}}</td>
                                <td>{{$item->invoices->status}}</td>
                                <td>{{$item->email}}</td>
                            </tr>
                        @endforeach
                    </tbody> --}}
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
            order: [[ 3, 'desc' ]],
            ajax: "{{route('admin.link.dtable.member', ['id' => $id])}}",
            columns: [{
                    data: 'full_name',
                    name: 'nama lengkap'
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
            ]
        });
    });
</script>
@endpush