<div class="col-md-11 col-lg-11">
    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3 card">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Edit User Information
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <button type="submit" name="save" value="1"
                            class="border-0 btn-transition btn btn-outline-success">Save</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-user-name" class="form-label fw-bolder mb-0 required">{{ __('Full Name') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $user->name ?? old('name') }}" autofocus name="name"
                            id="input-user-name">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-user-email" class="form-label fw-bolder mb-0 required">{{ __('Email Address') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $user->email ?? old('email') }}" name="email"
                            id="input-user-email">
                    </div>
                </div>
                <hr />
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-user-role" class="form-label fw-bolder mb-0 required">{{ __('Role') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $user->role }}" readonly="true" name="role"
                            id="input-user-role">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
