<!--make list of sub members-->
<div class="list-group">
    @if ($particapants->isEmpty())
        <!--if no sub members : text alert-->
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Tidak ada peserta</h4>
            <p>Belum ada peserta yang terdaftar.</p>
            <hr>
        </div>
    @else
        @foreach ($particapants as $member)
            <a href="#" class="list-group-item list-group-item-action" aria-current="true">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $member->full_name }}</h5>
                    <small>Peserta {{ $loop->iteration }}</small>
                </div>
                <p class="mb-1">Phone: {{ $member->contact_number }}</p>
                <small>{{ $member->corporation }}</small>
            </a>
        @endforeach
    @endif
</div>
