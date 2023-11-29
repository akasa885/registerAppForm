<ul class="body-tabs body-tabs-layout tabs-animated body-tabs-animated nav">
    @foreach ($menu as $item)
    <li class="nav-item">
        <a href="{{ $item['link'] }}" class="nav-link show @if($item['route'] == Route::currentRouteName()) active @endif" id="tab-{{ $loop->iteration }}" aria-selected="@if($item['route'] == Route::currentRouteName()) true @else false @endif">
            <span>{{ $item['label'] }}</span>
        </a>
    </li>    
    @endforeach
</ul>