<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Member;
use App\Http\Traits\FormatNumberTrait;

class DashboardController extends Controller
{
    use FormatNumberTrait;

    private $user;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->user = auth()->user();
        $membersCount = null;
        $viewdStatus = null; // will be drop, up, or same
        $linkCount = $this->getLinkCountLastOneYear();
        $memberFreeCount = $this->getMemberCountLinkTypeFreeLastOneYear();
        $memberPayCount = $this->getMemberCountLInkTypePayLastOneYear();
        $viewedLinkCount = $this->getViewedLinkCountLastTwoMonth();

        // comparing the last two month. is last month bigger than previous month?
        $lastMonth = Carbon::now()->copy()->subMonthNoOverflow()->format('Y-m');
        $previousMonth = Carbon::now()->subMonths(2)->format('Y-m');
        $viewdStatus = $this->compareLastTwoMonth($lastMonth, $previousMonth);

        // summing array value
        $linkCount = array_sum($linkCount);
        $memberFreeCount = array_sum($memberFreeCount);
        $memberPayCount = array_sum($memberPayCount);

        $membersCount = $memberFreeCount + $memberPayCount;
        $lastViewedLinkCount = end($viewedLinkCount);

        // format number
        $linkCount = $this->shorterCounting($linkCount);
        $membersCount = $this->shorterCounting($membersCount);
        $lastViewedLinkCount = $this->shorterCounting($lastViewedLinkCount);
        
        return view('admin.pages.home', compact('linkCount', 'membersCount', 'viewdStatus', 'lastViewedLinkCount'));
    }

    private function getLinkCountLastOneYear()
    {
        $linkCount = Link::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as total')
            ->when(Gate::allows('isAdmin'), function ($query) {
                return $query->where('created_by', $this->user->id);
            })
            ->whereRaw('created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)')
            ->whereMonth('created_at', '<', Carbon::now()->format('m'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $linkCount = $linkCount->pluck('total', 'month')->toArray();

        $linkCount = $this->fillMissingMonth($linkCount, Carbon::now()->subMonths(12)->format('Y-m'), Carbon::now()->subMonth()->format('Y-m'));
        
        return $linkCount;
    }

    private function getMemberCountLinkTypeFreeLastOneYear()
    {
        $memberCount = Member::selectRaw('DATE_FORMAT(members.created_at, "%Y-%m") as month, count(*) as total')
            ->join('links', 'links.id', '=', 'members.link_id')
            ->where('links.link_type', 'free')
            ->when(Gate::allows('isAdmin'), function ($query) {
                return $query->where('created_by', $this->user->id);
            })
            ->whereRaw('members.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)')
            ->whereMonth('members.created_at', '<', Carbon::now()->format('m'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $memberCount = $memberCount->pluck('total', 'month')->toArray();

        $memberCount = $this->fillMissingMonth($memberCount, Carbon::now()->subMonths(12)->format('Y-m'), Carbon::now()->subMonth()->format('Y-m'));

        return $memberCount;
    }

    public function getMemberCountLInkTypePayLastOneYear()
    {
        // member = link, member = invoice, invoice.status = 2
        $memberCount = Member::selectRaw('DATE_FORMAT(members.created_at, "%Y-%m") as month, count(*) as total')
            ->join('links', 'links.id', '=', 'members.link_id')
            ->join('invoices', 'invoices.member_id', '=', 'members.id')
            ->where('links.link_type', 'pay')
            ->when(Gate::allows('isAdmin'), function ($query) {
                return $query->where('created_by', $this->user->id);
            })
            ->where('invoices.status', 2)
            ->whereRaw('members.created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)')
            ->whereMonth('members.created_at', '<', Carbon::now()->format('m'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $memberCount = $memberCount->pluck('total', 'month')->toArray();

        $memberCount = $this->fillMissingMonth($memberCount, Carbon::now()->subMonths(12)->format('Y-m'), Carbon::now()->subMonth()->format('Y-m'));

        return $memberCount;
    }

    private function getViewedLinkCountLastTwoMonth()
    {
        $viewedLinkCount = Link::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, sum(viewed_count) as total')
            ->when(Gate::allows('isAdmin'), function ($query) {
                return $query->where('created_by', $this->user->id);
            })
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

        dd($viewedLinkCount);

        if (( count($viewedLinkCount) > 1 ) && $viewedLinkCount[$lastMonth] > $viewedLinkCount[$previousMonth]) {
            $viewdStatus = 'up';
        } elseif (( count($viewedLinkCount) > 1 ) && $viewedLinkCount[$lastMonth] < $viewedLinkCount[$previousMonth]) {
            $viewdStatus = 'down';
        } else {
            $viewdStatus = 'same';
        }

        return $viewdStatus;
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
