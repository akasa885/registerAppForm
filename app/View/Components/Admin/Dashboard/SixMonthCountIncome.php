<?php

namespace App\View\Components\Admin\Dashboard;

use Illuminate\View\Component;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SixMonthCountIncome extends Component
{
    public $title = 'Six Month Count Transaction';
    public $subtitle = 'Count Income';
    public $dataChart = [];
    private $cacheTime = 300; // 5 minutes

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null, $subtitle = null)
    {
        $this->title = $title ?? 'Six Month Count Transaction';
        $this->subtitle = $subtitle ?? 'Count Income';
        
        $this->dataChart = Cache::remember('six_month_income', $this->cacheTime, function () {
            return $this->getDataChart();
        });
    }

    private function getDataChart()
    {
        list($dateFRange, $dateRange) = $this->getDateRange();
        
        $startDate = Carbon::createFromFormat('Y-m', $dateRange[0])->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', end($dateRange))->endOfMonth();

        // Single optimized query
        $results = DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("COALESCE(SUM(net_total), 0) as total")
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->pluck('total', 'month');

        // Fill missing months with 0
        $data = collect($dateRange)->map(function ($month) use ($results) {
            return (int) ($results[$month] ?? 0);
        })->toArray();

        return [
            'labels' => collect($dateFRange),
            'datasets' => [
                [
                    'label' => 'Income',
                    'backgroundColor' => '#4e73df',
                    'hoverBackgroundColor' => '#2e59d9',
                    'borderColor' => '#4e73df',
                    'data' => collect($data),
                ],
            ],
        ];
    }

    private function getDateRange()
    {
        $dateRangeFormatted = [];
        $dateRange = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $dateRangeFormatted[] = $date->format('M Y');
            $dateRange[] = $date->format('Y-m');
        }

        return [$dateRangeFormatted, $dateRange];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.dashboard.six-month-count-income');
    }
}
