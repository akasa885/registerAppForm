<div class="col-md-11 col-lg-11">
    <div class="mb-3 card">
        <div class="card-header-tab card-header-tab-animation card-header">
            <div class="card-header-title">
                <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                Edit Website Info
            </div>
            <div class="btn-actions-pane-right">
                <div class="nav">
                    <button type="submit" name="status" value="1"
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
                    <input type="text" class="form-control" value="{{ old('web_name') }}" autofocus name="web_name"
                    id="input-website-name">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-3 d-flex align-items-center">
                    <label for="input-copyright" class="form-label fw-bolder mb-0 required">Copyright</label>
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control" value="{{ old('copyright') }}" autofocus name="copyright"
                    id="input-copyright">
                </div>
            </div>
        </div>
    </div>
</div>