<div class="col-md-11 col-lg-11">
    <form action="{{ route('admin.setting.update') }}" method="POST">
        @csrf
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
                    Edit Website Info
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
                        <label for="input-website-name" class="form-label fw-bolder mb-0 required">Nama Website</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $information['sitename'] ?? old('sitename') }}" autofocus name="sitename"
                            id="input-website-name">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-copyright" class="form-label fw-bolder mb-0 required">Copyright</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $information['copyright'] ?? old('copyright') }}" name="copyright"
                            id="input-copyright">
                    </div>
                </div>
                <hr />
                <span class="card-title fsize-1">SEO</span>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-website-description" class="form-label fw-bolder mb-0 required">Deskripsi
                            Website</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control"
                            value="{{ $information['description'] ?? old('sitedescription') }}" name="sitedescription"
                            id="input-website-description">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-website-keywords" class="form-label fw-bolder mb-0 required">Kata Kunci
                            Website</label>
                    </div>
                    <div class="col-md-9">
                        <select class="form-select" id="validationKeywords" name="keywords[]" multiple
                            data-allow-new="true" multiple data-allow-clear="true" data-max="10">
                            <option value=""></option>
                            @if (!empty($keywords))
                                @foreach ($keywords as $keyword)
                                    <option value="{{ $keyword }}" selected>{{ $keyword }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div class="invalid-feedback">Please select a valid keywords.</div>
                        {{-- <input type="text" class="form-control" value="{{ old('web_keywords') }}" name="web_keywords"
                    id="input-website-keywords"> --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
