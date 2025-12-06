<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Member;
use App\Http\Traits\FormatNumberTrait;

class DashboardController extends Controller
{
    use FormatNumberTrait;

    private $user;
    private $cacheTime = 300; // 5 minutes

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->user = auth()->user();
        $userId = $this->user->id;
        $isAdmin = Gate::allows('isAdmin');
        
        $cacheKey = $isAdmin ? "dashboard_admin_{$userId}" : "dashboard_superadmin";
        
        $dashboardData = Cache::remember($cacheKey, $this->cacheTime, function () use ($isAdmin) {
            return $this->getDashboardData($isAdmin);
        });

        return view('admin.pages.home', $dashboardData);
    }

    private function getDashboardData($isAdmin)
    {
        $startDate = Carbon::now()->subMonths(12)->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();

        // Single optimized query for link count
        $linkCount = $this->getOptimizedLinkCount($startDate, $endDate, $isAdmin);

        // Combined query for member counts
        $memberCounts = $this->getOptimizedMemberCounts($startDate, $endDate, $isAdmin);

        // Optimized viewed count
        $viewedData = $this->getOptimizedViewedData($isAdmin);

        $membersCount = $memberCounts['free'] + $memberCounts['pay'];

        return [
            'linkCount' => $this->shorterCounting($linkCount),
            'membersCount' => $this->shorterCounting($membersCount),
            'viewdStatus' => $viewedData['status'],
            'lastViewedLinkCount' => $this->shorterCounting($viewedData['lastCount'])
        ];
    }

    private function getOptimizedLinkCount($startDate, $endDate, $isAdmin)
    {
        $query = Link::whereBetween('created_at', [$startDate, $endDate]);
        
        if ($isAdmin) {
            $query->where('created_by', $this->user->id);
        }

        return $query->count();
    }

    private function getOptimizedMemberCounts($startDate, $endDate, $isAdmin)
    {
        $baseQuery = DB::table('members')
            ->join('links', 'links.id', '=', 'members.link_id')
            ->whereBetween('members.created_at', [$startDate, $endDate]);

        if ($isAdmin) {
            $baseQuery->where('links.created_by', $this->user->id);
        }

        // Single query to get both counts
        $counts = $baseQuery
            ->selectRaw("
                COUNT(CASE WHEN links.link_type = 'free' THEN 1 END) as free_count,
                COUNT(CASE WHEN links.link_type = 'pay' AND EXISTS (
                    SELECT 1 FROM invoices 
                    WHERE invoices.member_id = members.id 
                    AND invoices.status = 2
                ) THEN 1 END) as pay_count
            ")
            ->first();

        return [
            'free' => (int) $counts->free_count,
            'pay' => (int) $counts->pay_count
        ];
    }

    private function getOptimizedViewedData($isAdmin)
    {
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');
        $previousMonth = Carbon::now()->subMonths(2)->format('Y-m');

        $query = Link::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COALESCE(SUM(viewed_count), 0) as total
            ")
            ->whereRaw("DATE_FORMAT(created_at, '%Y-%m') IN (?, ?)", [$previousMonth, $lastMonth]);

        if ($isAdmin) {
            $query->where('created_by', $this->user->id);
        }

        $results = $query->groupBy('month')->pluck('total', 'month')->toArray();

        $lastCount = (int) ($results[$lastMonth] ?? 0);
        $prevCount = (int) ($results[$previousMonth] ?? 0);

        $status = $lastCount > $prevCount ? 'up' : ($lastCount < $prevCount ? 'down' : 'same');

        return [
            'lastCount' => $lastCount,
            'status' => $status
        ];
    }

    private function fillMissingMonth($linkCount, $startMonthYear = null, $endMonthYear = null)
    {
        $months = $this->getMonths();

        if ($startMonthYear) {
            $startMonth = Carbon::parse($startMonthYear)->format('Y-m');
            $endMonth = Carbon::parse($endMonthYear)->format('Y-m');
        } else {
            $startMonth = Carbon::now()->subYear()->format('Y-m');
            $endMonth = Carbon::now()->format('Y-m');
        }

        $months = array_filter($months, function ($month) use ($startMonth, $endMonth) {
            return ($month >= $startMonth && $month <= $endMonth);
        });

        foreach ($months as $month) {
            if (!isset($linkCount[$month])) {
                $linkCount[$month] = 0;
            }
        }

        ksort($linkCount);

        return $linkCount;
    }

    private function getMonths()
    {
        $months = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        return $months;
    }
}
