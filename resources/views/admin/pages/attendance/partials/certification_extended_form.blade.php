<div id="certificate_extended" class="@if ( !old('cert_confirm', null) ) d-none @endif">
    <!--begin::input group price certificate-->
    <div class="row mb-3">
        <div class="col-md-3 d-flex align-items-center">
            <label for="input-date-2" class="form-label fw-bolder mb-0 required">Price</label>
        </div>
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="price_certificate" class="form-control" id="input-price" min="10000" value="{{ old('price_certificate', $attendance->price_certificate ?? null) }}">
            </div>
        </div>
    </div>
    <!--end::input group price certificate-->
    <!--begin::input group certificate short info-->
    <div class="row mb-3">
        <div class="col-md-3 d-flex align-items-center">
            <label for="input-date-2" class="form-label fw-bolder mb-0 required">Informasi Tambahan Pembayaran</label>
        </div>
        <div class="col-md-9">
            <textarea name="payment_information" placeholder="informasi tambahan" class="my-editor form-control"
                id="my-editor-1" cols="10" rows="3">{{ old('payment_information', $attendance->payment_information ?? null) }}</textarea>
        </div>
    </div>
    <!--end::input group certificate short info-->
    <!--begin::checkbox group is payment gateway-->
    <div class="row mb-3">
        <div class="col-md-3 d-flex align-items-center">
            <label for="input-date-2" class="form-label fw-bolder mb-0 required">Pembayaran Otomatis ?</label>
        </div>
        <div class="col-md-9">
            <div class="form-check">
                <input class="form-check-input" name="is_using_payment_gateway" type="checkbox" id="confirm_payment_gateway">
                <label class="form-check-label" for="confirm_payment_gateway">
                    Ya
                </label>
            </div>
        </div>
    </div>
    <!--end::checkbox group is payment gateway-->
    <hr />
</div>