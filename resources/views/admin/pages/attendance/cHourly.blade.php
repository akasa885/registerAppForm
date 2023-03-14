<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Add new attandance session hourly
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <button type="submit" name="status" value="create"
                            class="border-0 btn-transition btn btn-outline-success">Buat</button>
                        <a href="{{ route('admin.users.view') }}"
                            class="border-0 btn-transition  btn btn-outline-danger">Cancel</a>

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
                <div class="row mb-3">

                    <div class="col-md-3 d-flex align-items-center">
                        <label for="select-event" class="form-label fw-bolder mb-0">Pilih Acara : </label>
                    </div>
                    <div class="col-md-9">
                        <select name="selected_event" id="select-event" class="form-control-sm form-control">
                            @foreach ($links as $link)
                                <option value="{{ $link->link_path }}">{{ ucfirst($link->title) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-1" class="form-label fw-bolder mb-0">Pilih Tanggal dan Jam Buka : </label>
                    </div>
                    <div class="col-md-9">
                        <input type="datetime-local" name="datetime_start" id="input-date-1" format class="form-control-sm form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Pilih Tanggal dan Jam Tutup : </label>
                    </div>
                    <div class="col-md-9">
                        <input type="datetime-local" name="datetime_end" id="input-date-2" format class="form-control-sm form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
