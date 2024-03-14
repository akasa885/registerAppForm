@extends('admin.layouts.app')

@push('up_scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@section('content')
    <!--being::header title-->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-car icon-gradient bg-mean-fruit">
                    </i>
                </div>
                <div>Analytics Dashboard
                    <div class="page-title-subheading">Admin dashboard components
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <button type="button" data-toggle="tooltip" title="" data-placement="bottom"
                    class="btn-shadow mr-3 btn btn-dark" data-original-title="Welcome">
                    <i class="fa fa-star"></i>
                </button>
            </div>
        </div>
    </div>
    <!--end::header title-->
    <!--begin::row info 1-->
    <div class="row">
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-midnight-bloom">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Total Events</div>
                        <div class="widget-subheading">1 Tahun Terakhir</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ $linkCount }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-mixed-hopes">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Total Participants</div>
                        <div class="widget-subheading">1 Tahun Terakhir</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white"><span>{{ $membersCount }}</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-4">
            <div class="card mb-3 widget-content bg-grow-early">
                <div class="widget-content-wrapper text-white">
                    <div class="widget-content-left">
                        <div class="widget-heading">Viewed Events Form</div>
                        <div class="widget-subheading">1 Bulan Terakhir</div>
                    </div>
                    <div class="widget-content-right">
                        <div class="widget-numbers text-white" style="display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 8px;">
                            @if ($viewdStatus == 'up')
                            <i class="pe-7s-up-arrow"> </i>
                            @elseif ($viewdStatus == 'down')
                            <i class="pe-7s-bottom-arrow"> </i>
                            @else
                            <i class="pe-7s-less"> </i>
                            @endif
                            <span>{{ $lastViewedLinkCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::row info 1-->
    <!--begin::row info 2-->
    <div class="row">
        <x-admin.dashboard.top10-list-event-link />
        <x-admin.dashboard.six-month-count-income />
    </div>
    <!--end::row info 2-->
@endsection
