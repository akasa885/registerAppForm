<div class="col-md-12">
    <div class="card mb-3">
        <div class="card-header d-flex flex-column align-items-md-start align-items-sm-center py-2">
            <span class="card-title mb-1 fsize-1">{{ $title }}</span>
            <span class="card-subtitle" style="font-size:.75rem !important;">{{ $subtitle }}</span>
        </div>
        <div class="card-body">
            <div class="object-fill">
                <div id="ytd-chart"></div>
            </div>
        </div>
    </div>
</div>

<script>
    var ytdOptions = {
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            }
        },
        series: [{
                name: 'Income',
                type: 'area',
                data: {!! json_encode($dataChart['income']) !!}
            },
            {
                name: 'Event Count',
                type: 'line',
                data: {!! json_encode($dataChart['eventCount']) !!}
            }
        ],
        xaxis: {
            categories: {!! json_encode($dataChart['labels']) !!},
            title: {
                text: 'Month'
            }
        },
        yaxis: [{
                title: {
                    text: 'Income (Rp)'
                },
                labels: {
                    formatter: function(val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Event Count'
                },
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            }
        ],
        tooltip: {
            shared: true,
            intersect: false,
            y: [{
                    formatter: function(val) {
                        return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                },
                {
                    formatter: function(val) {
                        return val + " events";
                    }
                }
            ]
        },
        title: {
            text: 'Year-to-Date Trends',
            align: 'center',
            style: {
                fontSize: '16px',
                fontWeight: 'bold'
            }
        },
        subtitle: {
            text: 'Income and Event Count Performance',
            align: 'center'
        },
        theme: {
            mode: 'light',
            palette: 'palette2'
        },
        colors: ['#008FFB', '#00E396'],
        stroke: {
            curve: 'smooth',
            width: [0, 3]
        },
        fill: {
            type: ['gradient', 'solid'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'center'
        },
        dataLabels: {
            enabled: false
        }
    };

    var ytdChart = new ApexCharts(document.querySelector("#ytd-chart"), ytdOptions);
    ytdChart.render();
</script>
