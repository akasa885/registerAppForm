<?php

namespace App\View\Components\Admin\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;

class YtdTrendChart extends Component
{
    public $title;
    public $subtitle;
    public $dataChart = [];
    private $cacheTime = 300; // 5 minutes
    private $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null, $subtitle = null)
    {
        $this->user = auth()->user();
        $this->title = $title ?? 'Year-to-Date Performance';
        $this->subtitle = $subtitle ?? 'Income and Event Count Trends from ' . now()->format('Y');

        $isAdmin = Gate::allows('isAdmin');
        $userId = $this->user->id;
        $cacheKey = $isAdmin ? "ytd_chart_admin_{$userId}" : "ytd_chart_superadmin";

        $this->dataChart = Cache::remember($cacheKey, $this->cacheTime, function () use ($isAdmin) {
            return $this->getYTDChartData($isAdmin);
        });
    }

    private function getYTDChartData($isAdmin)
    {
        $startOfYear = Carbon::now()->startOfYear();
        $today = Carbon::now();

        // Get monthly income data from orders (like SixMonthCountIncome)
        $incomeQuery = DB::table('orders')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(net_total), 0) as total")
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startOfYear, $today]);

        if ($isAdmin) {
            $incomeQuery->where('created_by', $this->user->id);
        }

        $incomeData = $incomeQuery->groupBy('month')->pluck('total', 'month')->toArray();

        // Get monthly event count data
        $eventQuery = DB::table('links')
            ->selectRaw("DATE_FORMAT(event_date, '%Y-%m') as month, COUNT(*) as count")
            ->whereBetween('event_date', [$startOfYear, $today])
            ->where('link_type', 'pay');

        if ($isAdmin) {
            $eventQuery->where('created_by', $this->user->id);
        }

        $eventData = $eventQuery->groupBy('month')->pluck('count', 'month')->toArray();

        // Generate all months from start of year to current month
        $labels = [];
        $income = [];
        $eventCount = [];

        $currentMonth = Carbon::parse($startOfYear);
        while ($currentMonth <= $today) {
            $monthKey = $currentMonth->format('Y-m');
            $labels[] = $currentMonth->format('M Y');
            $income[] = (float) ($incomeData[$monthKey] ?? 0);
            $eventCount[] = (int) ($eventData[$monthKey] ?? 0);
            $currentMonth->addMonth();
        }

        return [
            'labels' => $labels,
            'income' => $income,
            'eventCount' => $eventCount
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.dashboard.ytd-trend-chart');
    }
}
