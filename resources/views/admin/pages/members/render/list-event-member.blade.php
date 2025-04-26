<div class="list-group">
    @if ($links->isEmpty())
        <!--if no sub members : text alert-->
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Tidak ada acara diikuti</h4>
            <p>Belum ada data</p>
            <hr>
        </div>
    @else
        @foreach ($links as $link)
            <a href="javascript:void(0)" class="list-group-item list-group-item-action" aria-current="true">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1" style="font-size: 18px">{{ $link->full_name }}</h5>
                    <small># {{ $loop->iteration }}</small>
                </div>
                <p class="mb-1">Acara: {{ Str::limit($link->link->title, 45) }}</p>
                <small>Institusi : {{ $link->corporation }} / <strong>Tipe : {{ $link->link->link_type == 'pay' ? 'Bayar' : "Gratis" }}</strong></small>
            </a>
        @endforeach
    @endif
</div>