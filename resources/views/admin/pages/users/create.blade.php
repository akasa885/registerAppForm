@extends('admin.layouts.app')
@section('content')
<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf
    <input type="hidden" name="post_type" value="post">
<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Add New User
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <button type="submit" name="status" value="1" class="border-0 btn-transition btn btn-outline-success">Buat</button>                        
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
                        <input type="email" required class="form-control" value="{{old('email')}}" name="email" id="input-email">
                    </div>
                    <div class="mb-3">
                        <label for="input-name" class="form-label">Name</label>                            
                        <input type="text" required class="form-control" value="{{old('name')}}" name="name" id="input-name">
                    </div>
                    <div class="col-md-4 px-0 mb-3">
                        <label for="select-role" class="form-label">Role</label>
                        <select name="role" id="select-role" class="form-control">
                            @for ($i = 0; $i < count($role); $i++)
                            <option value="{{$role[$i]}}">{{ucfirst($role[$i])}}</option>    
                            @endfor
                        </select>
                    </div>                    
                    <div class="mb-3 col-md-6 px-0">
                        <label for="input-pass" class="form-label">Password</label>
                        <input type="password" required class="form-control" value="{{old('password')}}" name="password" id="input-pass" placeholder="masukkan password">
                    </div>
                    <div class="mb-3 col-md-6 px-0">
                        <label for="input-pass-2" class="form-label">Confirmation Password</label>
                        <input type="password" required class="form-control" value="{{old('password_confirmation')}}"  name="password_confirmation" id="input-pass-2" placeholder="masukkan password kembali">
                    </div>
                </div>                    
            </div>                              
    </div>            
</div>
</form>
@endsection