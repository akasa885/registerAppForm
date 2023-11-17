<!-- Start Content edit -->
<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
        <div class="mb-3 card">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    <i class="header-icon lnr-apartment icon-gradient bg-love-kiss"> </i>
                    Form Pendaftaran Customisasi
                </div>
                <div class="btn-actions-pane-right">
                    <div class="nav">
                        <button type="submit" name="status" value="1"
                            class="border-0 btn-transition btn btn-outline-success">Save</button>
                        <a href="{{ route('admin.link.view') }}"
                            class="border-0 btn-transition  btn btn-outline-danger">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Judul</label>
                    <input type="text" required class="form-control" value="{{ $link_detail->title }}" name="title"
                        id="title">
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Deskripsi</label>
                    <textarea required name="desc" placeholder="deskripsi acara" class="my-editor form-control" id="my-editor-3"
                        cols="30" rows="10">{!! $link_detail->description !!}</textarea>
                </div>
                <div class="mb-3">
                    <label for="desc" class="form-label">Infomarsi Acara (email)</label>
                    <textarea required name="registration_info" placeholder="informasi acara" class="my-editor form-control"
                        id="my-editor-4" cols="30" rows="10">{!! $link_detail->registration_info !!}</textarea>
                </div>
                @foreach ($link_detail->mails as $item)
                    @if ($item->type == 'confirmation')
                        <div class="mb-3">
                            <label for="desc" class="form-label">Pesan Email Permintaan Pembayaran</label>
                            <textarea required name="email_confirmation"
                                placeholder="isikan pesan email yang dikirim untuk pemberitahuan upload bayar" class="my-editor form-control"
                                id="my-editor-1" cols="30" rows="5">{!! $item->information !!}</textarea>
                        </div>
                    @endif
                    @if ($item->type == 'confirmed')
                        <div class="mb-3">
                            <label for="desc" class="form-label">Pesan Email Pembayaran
                                Terkonfirmasi</label>
                            <textarea required name="email_confirmed" placeholder="isikan pesan email yang dikirim untuk pemberitahuan upload bayar"
                                class="my-editor form-control" id="my-editor-2" cols="30" rows="5">{!! $item->information !!}</textarea>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3 d-flex flex-column feature-img">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Banner
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="fileinput-new thumbnail" id="holder" style="max-width: 200px; max-height: 150px;">
                    <img id="previewimg_thumb"
                        src="
            @php $temp = explode('/', $link_detail->banner);
            $size = sizeof($temp);
            if($link_detail->banner == null){
                echo asset('/images/default/no-image.png');
            }else{
                $url2= str_replace(basename($link_detail->banner) , '', $link_detail->banner  ) ;
                $url2=$url2.'thumbs/'.basename($link_detail->banner);
            
                echo $url2;
            } @endphp"
                        alt="banner" />
                </div>
            </div>
            <div class="col p-4">
                <div class="input-group">
                    <span class="input-group-btn">
                        <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                            <i class="fa fa-picture-o"></i> Choose
                        </a>
                    </span>
                    <input id="thumbnail" class="form-control" value="{{ $link_detail->banner }}" type="text"
                        name="filepath">
                </div>
            </div>
        </div>
        <!--begin:: kuota-->
        <div class="card mb-3 d-flex flex-column">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Batas Kuota Pendaftar (Opsional)
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="form-group">
                    <input type='number' min="0" name="member_limit"
                        value="{{ old('member_limit', $link_detail->member_limit ?? 0) }}" required class="form-control"
                        id='member-limit'>
                    <small class="form-text text-muted">Beri angka 0, apabila tidak ada batasan</small>
                </div>
            </div>
        </div>
        <!--end:: kuota-->
        <!--begin:: price-->
        <div class="card mb-3 d-flex flex-column">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Harga Tiket
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="form-group">
                    <input type='number' min="100" name="price" value="{{ old('price', $link_detail->price) }}"
                        required class="form-control" id='ticket-price'>
                </div>
            </div>
        </div>
        <!--end::price-->
        <div class="card mb-3 d-flex flex-column">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Tanggal Pendaftaran Dibuka
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="form-group">
                    <input type='text' name="open_date"
                        value="{{ date('d-m-Y', strtotime($link_detail->active_from)) }}" required
                        class="form-control" id='datepicker-1' placeholder="dd-mm-yy">
                </div>
            </div>
        </div>
        <div class="card mb-3 d-flex flex-column">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Tanggal Pendaftaran Ditutup
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="form-group">
                    <input type='text' name="close_date"
                        value="{{ date('d-m-Y', strtotime($link_detail->active_until)) }}" required
                        class="form-control" id='datepicker-2' placeholder="dd-mm-yy">
                </div>
            </div>
        </div>
        <div class="card mb-3 d-flex flex-column">
            <div class="card-header-tab card-header-tab-animation card-header">
                <div class="card-header-title">
                    Tanggal Mulai Acara
                </div>
            </div>
            <div class="col-auto align-self-center my-2">
                <div class="form-group">
                    <input type='text' name="event_date" value="{{ $link_detail->event_date != null ? date('d-m-Y', strtotime($link_detail->event_date)) : '' }}" required
                        class="form-control" id='datepicker-3' placeholder="dd-mm-yy">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Content edit -->
