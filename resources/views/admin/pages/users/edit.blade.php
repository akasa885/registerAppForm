@extends('admin.layouts.app')
@section('content')
<form action="{{ route('admin.users.update', ['id' => $user->id]) }}" method="POST">
    @csrf
    <input type="hidden" name="post_type" value="post">
<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    User Setting
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <button type="submit" name="status" value="1" class="border-0 btn-transition btn btn-outline-success">Simpan</button>                        
                        <a href="{{ route('admin.users.view') }}" class="border-0 btn-transition  btn btn-outline-danger">Cancel</a>

                    </div>
                </div>
            </div>
        </div>    
    </div>                
    <div class="col-md-10">            
            <div class="card mb-3">
                <div class="card-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="input-email" class="form-label">Email</label>
                        <input type="email" required class="form-control" value="{{$user->email}}" name="email" id="input-email">
                    </div>
                    <div class="mb-3">
                        <label for="input-name" class="form-label">Name</label>                            
                        <input type="text" required class="form-control" value="{{$user->name}}" name="name" id="input-name">
                    </div>
                    <div class="col-md-4 px-0 mb-3">
                        <label for="select-role" class="form-label">Role</label>
                        <select name="role" id="select-role" class="form-control">
                            @foreach ($role as $item)
                                @if($item == $user->role)
                                    <option value="{{$item}}" selected="selected">{{ucwords($item)}}</option>
                                @else
                                    <option value="{{$item}}">{{ucwords($item)}}</option>
                                @endif                                
                            @endforeach                            
                        </select>
                    </div>
                    <hr />
                    <div class="mb-3 col-md-6 px-0">
                        <label for="new-pass" class="form-label h6 fw-bold">Set New Password</label>
                        <input type="password" class="form-control" name="new_password" id="new-pass" placeholder="masukkan password baru">
                    </div>
                </div>                    
            </div>                              
    </div>            
</div>
</form>
@endsection