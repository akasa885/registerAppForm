<?php

namespace App\View\Components\Admin\Dashboard;

use Illuminate\View\Component;
use App\Models\Order;
use App\Models\Link;
use Carbon\Carbon;

class SixMonthCountIncome extends Component
{
    public $title = 'Six Month Count Transaction';
    public $subtitle = 'Count Income';
    public $dataChart = [];
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($itle = null, $subtitle = null)
    {
        $this->title = $title ?? 'Six Month Count Transaction';
        $this->subtitle = $subtitle ?? 'Count Income';
        $this->dataChart = $this->getDataChart();
        // dd($this->dataChart);
    }

    private function getDataChart()
    {
        list($dateFRange, $dateRange) = $this->getDateRange();
        $data = [];
        foreach ($dateRange as $key => $date) {
            $data[$key] = Order::where('status', 'completed')
                ->whereDate('created_at', 'like', $date . '%')
                ->sum('net_total');
        }

        $data = array_reverse($data);
        $dateFRange = array_reverse($dateFRange);

        $data = array_map(function ($item) {
            return intval($item);
        }, $data);

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
        $dateRange = [];
        $date = Carbon::now();
        for ($i = 0; $i < 6; $i++) {
            $dateRange['formatted'][] = $date->format('M Y');
            $dateRange['date'][] = $date->format('Y-m');
            $date->subMonth();
        }

        return [$dateRange['formatted'], $dateRange['date']];
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
