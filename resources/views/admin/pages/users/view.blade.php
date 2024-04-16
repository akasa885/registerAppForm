@extends('admin.layouts.app')
@section('title', $title)
@section('content')
        <div class="app-page-title">
            <div class="page-title-wrapper">
                <div class="page-title-heading">
                    <div class="page-title-icon">
                        <i class="pe-7s-diamond icon-gradient bg-strong-bliss">
                        </i>
                    </div>
                    <div>List user
                        <div class="page-title-subheading">This is an post panel that you can manage your Users
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
                        User List
                    </div>
                    @can('isSuperAdmin')
                    <div class="btn-actions-pane-right">
                        <div class="nav">
                            <a href="{{route('admin.users.create')}}" class="border-0 btn-transition btn btn-outline-success">Add New User</a>                          
                        </div>
                    </div>                    
                    @else
                      <div class="btn-actions-pane-right">
                          
                      </div>                                      
                    @endcan
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="data_users_side" class="mb-0 table display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Display Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>






          {{-- modal --}}
          <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
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
            ajax: "{{route('admin.users.dtb_list')}}",
            "columnDefs": [
                { "width": "20%", "targets": 0 }
            ],
            columns: [
                {                    
                    data: 'name',
                    name: 'display_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'role',
                    name: 'role'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'Options',
                    name: 'Options'
                }
            ]
        });
    });
    function deactivateAction(id) {        
        let url = "{{route('admin.users.deactive',['id' => ":id"])}}";
        url = url.replace(':id', id);        
        let confirmAction = confirm("Anda yakin ingin non aktifkan data ?");
        if (confirmAction) {
          $.ajax({
            type: "delete",
            url : url,
            data: {id : id},
            cache : false,
            success : function(data){
                if(data.success){
                    alert(data.message);
                    location.reload();
                }else{
                    alert('request gagal');
                }                
            }
          });
        } else {
          alert("Action canceled");
        }
    };
    function activateAction(id) {        
        let url = "{{route('admin.users.active',['id' => ":id"])}}";
        url = url.replace(':id', id);        
        let confirmAction = confirm("Anda yakin ingin aktifkan data ?");
        if (confirmAction) {
          $.ajax({
            type: "put",
            url : url,
            data: {id : id},
            cache : false,
            success : function(data){
                if(data.success){
                    alert(data.message);
                    location.reload();
                }else{
                    alert('request gagal');
                }                
            }
          });
        } else {
          alert("Action canceled");
        }
    };
</script>
@endpush