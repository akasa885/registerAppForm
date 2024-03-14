<div class="col-md-12 col-xl-6">
    <div class="card mb-3">
        <div class="card-header d-flex flex-column align-items-md-start align-items-sm-center py-2">
            <span class="card-title mb-1 fsize-1">{{ $title }}</span>
            {{-- <span class="card-subtitle" style="font-size:.75rem !important;">{{ $subtitle }}</span> --}}
        </div>
        <div class="card-body">
            <div class="object-fill">
                <div id="chart"></div>
            </div>
        </div>
    </div>
</div>


<script>
    var options = {
        chart: {
            type: 'area'
        },
        series: [{
            name: 'incomes',
            data: {!! $dataChart['datasets'][0]['data'] !!},
        }],
        xaxis: {
            categories: {!! $dataChart['labels'] !!}
        },
        // tooltip change each data
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }
            }
        },
        title: {
            text: 'Monthly Income'
        },
        subtitle: {
            text: 'Last 6 months'
        },
        theme: {
            mode: 'light',
            palette: 'palette1',
            monochrome: {
                enabled: false,
                color: '#255aee',
                shadeTo: 'light',
                shadeIntensity: 0.65
            },
        },
        stroke: {
            curve: 'smooth'
        },
        dataLabels: {
            enabled: false
        },
        markers: {
            size: 6,
            strokeWidth: 0,
            hover: {
                size: 9
            }
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        yaxis: {
            title: {
                text: 'Income'
            },
            labels: {
                formatter: function (val) {
                    // make shorter count: like K for thousand, M for million
                    if (val > 999 && val < 1000000) {
                        return (val / 1000).toFixed(1).toString() + 'K'
                    } else if (val > 999999) {
                        return (val / 1000000).toFixed(1).toString() + 'M'
                    } else {
                        return val.toString()
                    }
                }
            }
        }
    }

    var chart = new ApexCharts(document.querySelector("#chart"), options);

    chart.render();
</script>
