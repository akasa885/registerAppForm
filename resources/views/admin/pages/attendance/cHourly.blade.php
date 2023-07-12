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
                        <a href="{{ route('admin.attendance.view') }}"
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
                        <label for="input-date-1" class="form-label fw-bolder mb-0">Pilih Tanggal dan Jam Buka :
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="datetime-local" name="datetime_start" id="input-date-1" format
                            class="form-control-sm form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Pilih Tanggal dan Jam Tutup :
                        </label>
                    </div>
                    <div class="col-md-9">
                        <input type="datetime-local" name="datetime_end" id="input-date-2" format
                            class="form-control-sm form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Pesan Email Absensi
                            Terkonfirmasi</label>
                    </div>
                    <div class="col-md-9">
                        <textarea required name="confirmatiomn_mail" placeholder="informasi acara" class="my-editor form-control"
                            id="my-editor-1" cols="30" rows="10">{!! old('confirmatiomn_mail') !!}</textarea>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Terkonfirmasi Email ?</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-check">
                            <input class="form-check-input" name="mail_confirm" type="checkbox" id="confirm-cert">
                            <label class="form-check-label" for="confirm-email">
                                Ya
                            </label>
                        </div>
                        <div id="email-helper" class="form-text">Jika di centang maka email akan terkirim ke peserta</div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Konfirmasi Sertifikat ?</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-check">
                            <input class="form-check-input" name="cert_confirm" type="checkbox" id="confirm-cert">
                            <label class="form-check-label" for="confirm-cert">
                                Ya
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="//cdn.ckeditor.com/4.17.2/standard/ckeditor.js"></script>
    <script type="text/javascript">
        var options = {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
        };
        CKEDITOR.replace('my-editor-1', options);
    </script>
@endpush
