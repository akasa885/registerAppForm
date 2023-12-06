<?php

namespace App\View\Components\Admin\Dashboard;

use Illuminate\View\Component;
use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Top10ListEventLink extends Component
{
    public $formattedData = [];
    public $title = 'Top 10 List Event Link';
    public $subtitle = 'Peserta terbanyak dalam 1 Tahun Terakhir';
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null, $subtitle = null)
    {
        $this->title = $title ?? $this->title;
        $this->subtitle = $subtitle ?? $this->subtitle;
        $this->formattedData = $this->getLinks();
    }

    private function getLinks()
    {
        // last 365 days
        $links = Link::select('id', 'link_path', 'title', 'link_type', 'created_at')
            ->where('created_at', '>=', Carbon::now()->subDays(365))
            ->with('members')
            ->get();
        
        $linksFree = $links->filter(function ($link) {
            return $link->link_type == 'free';
        });

        $linksPay = $links->filter(function ($link) {
            return $link->link_type == 'pay';
        });

        // link pay, filter member on linkpay is invoices status = 2
        foreach ($linksPay as $pay) {
            $pay->filtered_members = $pay->members->filter(function ($member) {
                if ($member->invoices == null) {
                    return false;
                }

                return $member->invoices->status == 2;
            })->values();
        }

        // unset members on link pay
        foreach ($linksPay as $pay) {
            unset($pay->members);
            $pay->members = $pay->filtered_members;
            unset($pay->filtered_members);
        }

        // count members on link free & pay
        foreach ($linksFree as $free) {
            $free->count_members = $free->members->count();
            unset($free->members);
        }

        foreach ($linksPay as $pay) {
            $pay->count_members = $pay->members->count();
            unset($pay->members);
        }

        // merge link free & pay
        $links = $linksFree->merge($linksPay);

        // sort by count members
        $links = $links->sortByDesc('count_members')->values();

        // get top 10
        $links = $links->take(10);

        // format data
        $formattedData = [];

        foreach ($links as $link) {
            $formattedData[] = [
                'id' => $link->id,
                'link_url' => route('form.link.view', ['link' => $link->link_path]),
                'title' => $link->title,
                'link_type' => $link->link_type,
                'count_members' => $link->count_members,
                'created_at' => $link->created_at->format('d M Y'),
            ];
        }

        return $formattedData;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.dashboard.top10-list-event-link');
    }
}
