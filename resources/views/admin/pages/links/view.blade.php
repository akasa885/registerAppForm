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
    <div class="col-md-11 col-lg-11">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Link List
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <a href="{{route('admin.link.create')}}" class="border-0 btn-transition btn btn-outline-success">Add Link Pendaftaran</a>                          
                    </div>
                </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="data_users_side" class="mb-0 table display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 30%">Title</th>
                            <th style="width: 30%">Link</th>
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
            columns: [{
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
            ]
        });
    });
</script>
@endpush