<?php

namespace App\View\Components\Admin\Dashboard;

use Illuminate\View\Component;
use App\Models\Link;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Top10ListEventLink extends Component
{
    public $formattedData = [];
    public $title = 'Top 10 List Event Link';
    public $subtitle = 'Peserta terbanyak dalam 1 Tahun Terakhir';
    private $cacheTime = 300; // 5 minutes

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = null, $subtitle = null)
    {
        $this->title = $title ?? $this->title;
        $this->subtitle = $subtitle ?? $this->subtitle;
        
        $userId = Auth::id();
        $isAdmin = Auth::user()->can('isAdmin');
        $cacheKey = $isAdmin ? "top10_links_admin_{$userId}" : "top10_links_superadmin";
        
        $this->formattedData = Cache::remember($cacheKey, $this->cacheTime, function () {
            return $this->getLinks();
        });
    }

    private function getLinks()
    {
        $startDate = Carbon::now()->subDays(365);
        $userId = Auth::id();
        $isAdmin = Auth::user()->can('isAdmin');

        // Single optimized query
        $links = DB::table('links')
            ->select(
                'links.id',
                'links.link_path',
                'links.title',
                'links.link_type',
                'links.created_at',
                DB::raw("
                    CASE 
                        WHEN links.link_type = 'free' THEN (
                            SELECT COUNT(*) 
                            FROM members 
                            WHERE members.link_id = links.id
                        )
                        WHEN links.link_type = 'pay' THEN (
                            SELECT COUNT(*) 
                            FROM members 
                            INNER JOIN invoices ON invoices.member_id = members.id
                            WHERE members.link_id = links.id 
                            AND invoices.status = 2
                        )
                        ELSE 0
                    END as count_members
                ")
            )
            ->where('links.created_at', '>=', $startDate)
            ->when($isAdmin, function ($query) use ($userId) {
                return $query->where('links.created_by', $userId);
            })
            ->orderByDesc('count_members')
            ->limit(10)
            ->get();

        return $links->map(function ($link) {
            return [
                'id' => $link->id,
                'link_url' => route('form.link.view', ['link' => $link->link_path]),
                'title' => $link->title,
                'link_type' => $link->link_type,
                'count_members' => (int) $link->count_members,
                'created_at' => Carbon::parse($link->created_at)->format('d M Y'),
            ];
        })->toArray();
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
