<?php

namespace Mcms\Pages\Middleware;

use Carbon\Carbon;
use Closure;
use Mcms\Pages\Models\Page;

/**
 * Look for all pages about to be published and activate them
 *
 * Class PublishPage
 * @package Mcms\Pages\Middleware
 */
class PublishPage
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Page::where('published_at', '>=', Carbon::now())->update(['active'=> true]);

        return $next($request);
    }
}