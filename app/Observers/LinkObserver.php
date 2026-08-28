<?php

namespace App\Observers;

use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class LinkObserver
{
    /**
     * Handle the Link "created" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function created(Link $link)
    {
        $this->flushLinkCache($link);
    }

    /**
     * Handle the Link "updated" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function updated(Link $link)
    {
        $this->flushLinkCache($link);
    }

    /**
     * Handle the Link "deleted" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function deleted(Link $link)
    {
        //
    }

    /**
     * Handle the Link "restored" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function restored(Link $link)
    {
        //
    }

    /**
     * Handle the Link "force deleted" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function forceDeleted(Link $link)
    {
        //
    }

    private function flushLinkCache(Link $link)
    {
        // availabke cache is link_{link_path} and links
        Cache::forget('link_' . $link->link_path);
    }
}
