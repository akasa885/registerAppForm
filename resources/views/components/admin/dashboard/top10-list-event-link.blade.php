<div class="col-md-12 col-xl-6">
    <div class="card mb-3">
        <div class="card-header d-flex flex-column align-items-md-start align-items-sm-center py-2">
            <span class="card-title mb-1 fsize-1">{{ $title }}</span>
            <span class="card-subtitle" style="font-size:.75rem !important;">{{ $subtitle }}</span>
        </div>
        <div class="card-body px-2">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-borderless table-sm">
                    <thead>
                        <tr>
                            <th class="">#</th>
                            <th>Title</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($formattedData as $link)
                        <tr style="height: 3em;">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge badge-alternate mr-1">{{ $link['link_type'] }}</span> <a href="{{ $link['link_url'] }}">{{ Str::limit($link['title'], 50) }}</a> <br/>
                            </td>
                            <td>{{ $link['created_at'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{-- {{ $links->links() }} --}}
            </div>
        </div>
    </div>
</div>