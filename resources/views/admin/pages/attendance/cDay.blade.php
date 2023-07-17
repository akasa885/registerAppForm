<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Add new attandance session full day
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
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>{{ $message }}</strong>
                        <button type="button" style="height:-webkit-fill-available; width: 50px;" class="btn-close"
                            data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="path" class="form-label fw-bolder mb-0">https://form.test/attend/</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" name="" class="form-control" id="">
                    </div>
                </div> --}}
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
                        <label for="input-date" class="form-label fw-bolder mb-0">Pilih Tanggal : </label>
                    </div>
                    <div class="col-md-9">
                        <input type="date" name="date" id="input-date-1" format
                            class="form-control-sm form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Pesan Email Absensi
                            Terkonfirmasi</label>
                    </div>
                    <div class="col-md-9">
                        <textarea required name="confirmation_mail" placeholder="informasi acara" class="my-editor form-control"
                            id="my-editor-1" cols="30" rows="10">{!! old('confirmation_mail') !!}</textarea>
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
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-date-2" class="form-label fw-bolder mb-0">Bolehkan Yang Belum Registrasi
                            ?</label>
                    </div>
                    <div class="col-md-9">
                        <div class="form-check">
                            <input class="form-check-input" name="allow_non_register" type="checkbox"
                                id="allow-non-register">
                            <label class="form-check-label" for="allow-non-register">
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
    <script src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript">
        var type_image = 'type=Images&_token=' + '{{ csrf_token() }}';
        var options = {
            cloudServices_tokenUrl: '',
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '{{ route("admin.ajax.ck-upload-image")."?"}}' + type_image,
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='+ '{{ csrf_token() }}',
            removePlugins: 'easyimage',
            extraPlugins: 'simplebutton, image, justify',

        };
        CKEDITOR.replace('my-editor-1', options);
    </script>
@endpush
