<div class="col-md-11 col-lg-11">
    <form action="{{ route('admin.profile.update-password') }}" method="POST">
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
                    Change Password
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
                        <label for="input-user-old-password" class="form-label fw-bolder mb-0 required">{{ __('Old Password') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control"
                            autofocus name="current_password"
                            id="input-user-old-password">
                    </div>
                </div>
                <hr />
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-user-new-password" class="form-label fw-bolder mb-0 required">{{ __('New Password') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control"
                            name="password"
                            id="input-user-new-password">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-user-confirm-password" class="form-label fw-bolder mb-0 required">{{ __('Confirm Passowrd') }}</label>
                    </div>
                    <div class="col-md-9">
                        <input type="password" class="form-control"
                            name="password_confirmation"
                            id="input-user-confirm-password">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
