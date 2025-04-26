<div class="row">
    <div class="col-lg-12">
        <div class="row mb-3">
            <!--begin::nama-->
            <div class="col-lg-12">
                <div class="form-group">
                    <label for="nama">Nama Peserta</label>
                    <input type="text" name="nama" class="form-control" value="{{ $member->full_name }}" readonly>
                </div>
            </div>
            <!--end::nama-->
        </div>
        <div class="row">
            <form action="{{ $invoiceRoute }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="memberId" value="{{ $member->id }}">
                <div class="form-group">
                    <label for="bukti_transfer">Bukti Transfer</label>
                    <input type="file" name="bukti" class="form-control" required accept="image/*">
                </div>
                
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>
</div>