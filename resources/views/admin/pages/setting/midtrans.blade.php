<div class="col-md-11 col-lg-11">
    <form action="{{ route('admin.setting.midtrans.update') }}" method="POST">
        @csrf
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Edit Midtrans Setting
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
                        <label for="input-merchant-id" class="form-label fw-bolder mb-0 required">Merchant ID</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="{{ $midtransInfo['MIDTRANS_MERCHANT_ID'] ?? old('midtrans_merchant_id') }}" autofocus
                            name="midtrans_merchant_id" id="input-merchant-id">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-client-key" class="form-label fw-bolder mb-0 required">Client Key</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="{{ $midtransInfo['MIDTRANS_CLIENT_KEY'] ?? old('midtrans_client_key') }}"
                            name="midtrans_client_key" id="input-client-key">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="input-server-key" class="form-label fw-bolder mb-0 required">Server Key</label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="{{ $midtransInfo['MIDTRANS_SERVER_KEY'] ?? old('midtrans_server_key') }}"
                            name="midtrans_server_key" id="input-server-key">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 d-flex align-items-center">
                        <label for="select-environtment" class="form-label fw-bolder mb-0 required">Environtment</label>
                    </div>
                    <div class="col-md-9">
                        <select name="midtrans_environment" id="select-environtment"
                            class="form-control-sm form-control">
                            <option @if(!$midtransInfo['MIDTRANS_IS_PRODUCTION']) selected @endif value="sandbox">Sandbox</option>
                            <option @if($midtransInfo['MIDTRANS_IS_PRODUCTION']) selected @endif value="production">Production</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
