<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 09:25:57
 */

namespace Diepxuan\Core\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ClearCache
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (!\in_array(config('app.env'), ['production', 'staging'], true)) {
            // Artisan::call("cache:clear");
            // Artisan::call("view:clear");
            // Artisan::call("route:clear");
            Cache::flush();
        }

        return $next($request);
    }
}
