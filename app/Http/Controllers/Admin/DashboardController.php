<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Member;
use App\Http\Traits\FormatNumberTrait;

class DashboardController extends Controller
{
    use FormatNumberTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $membersCount = null;
        $viewdStatus = null; // will be drop, up, or same
        $linkCount = $this->getLinkCountLastOneYear();
        $memberFreeCount = $this->getMemberCountLinkTypeFreeLastOneYear();
        $viewedLinkCount = $this->getViewedLinkCountLastTwoMonth();

        // comparing the last two month. is last month bigger than previous month?
        $lastMonth = Carbon::now()->subMonth()->format('Y-m');
        $previousMonth = Carbon::now()->subMonths(2)->format('Y-m');
        $viewdStatus = $this->compareLastTwoMonth($lastMonth, $previousMonth);

        // summing array value
        $linkCount = array_sum($linkCount);
        $memberFreeCount = array_sum($memberFreeCount);

        $membersCount = $memberFreeCount;
        $lastViewedLinkCount = end($viewedLinkCount);

        // format number
        $linkCount = $this->shorterCounting($linkCount);
        $membersCount = $this->shorterCounting($membersCount);
        
        return view('admin.pages.home', compact('linkCount', 'membersCount', 'viewdStatus', 'lastViewedLinkCount'));
    }

    private function getLinkCountLastOneYear()
    {
        $linkCount = Link::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $linkCount = $linkCount->pluck('total', 'month')->toArray();

        $linkCount = $this->fillMissingMonth($linkCount);

        return $linkCount;
    }

    private function getMemberCountLinkTypeFreeLastOneYear()
    {
        $memberCount = Member::selectRaw('DATE_FORMAT(members.created_at, "%Y-%m") as month, count(*) as total')
            ->join('links', 'links.id', '=', 'members.link_id')
            ->where('links.link_type', 'free')
            ->where('members.created_at', '>=', Carbon::now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $memberCount = $memberCount->pluck('total', 'month')->toArray();

        $memberCount = $this->fillMissingMonth($memberCount);

        return $memberCount;
    }

    private function getViewedLinkCountLastTwoMonth()
    {
        $viewedLinkCount = Link::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, sum(viewed_count) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(2))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $viewedLinkCount = $viewedLinkCount->pluck('total', 'month')->toArray();
        $twoMonthAgo = Carbon::now()->subMonths(2)->format('Y-m');

        // if two month ago is not exist, then add it with value 0
        if (!isset($viewedLinkCount[$twoMonthAgo])) {
            $viewedLinkCount[$twoMonthAgo] = 0;
        }

        $today = Carbon::now()->format('Y-m');
        unset($viewedLinkCount[$today]);

        ksort($viewedLinkCount);

        // format all array value to int
        foreach ($viewedLinkCount as $key => $value) {
            $viewedLinkCount[$key] = (int) $value;
        }

        return $viewedLinkCount;
    }

    private function compareLastTwoMonth($lastMonth, $previousMonth)
    {
        $viewedLinkCount = $this->getViewedLinkCountLastTwoMonth();

        if ($viewedLinkCount[$lastMonth] > $viewedLinkCount[$previousMonth]) {
            $viewdStatus = 'up';
        } elseif ($viewedLinkCount[$lastMonth] < $viewedLinkCount[$previousMonth]) {
            $viewdStatus = 'down';
        } else {
            $viewdStatus = 'same';
        }

        return $viewdStatus;
    }

    private function fillMissingMonth($linkCount)
    {
        $months = $this->getMonths();

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

        for ($i = 0; $i < 12; $i++) {
            $months[] = Carbon::now()->subMonths($i)->format('Y-m');
        }

        return $months;
    }
}
