<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2026-04-12 16:59:26
 */

namespace Diepxuan\Core\Http\Middleware;

use Illuminate\Http\Request;
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
        if (!app()->environment('production', 'staging')) {
            // Xóa cache ứng dụng trực tiếp, nhanh hơn Artisan::call
            Cache::flush();

            // Xóa cache route nếu có
            if (file_exists($routesPath = app()->getCachedRoutesPath())) {
                app('files')->delete($routesPath);
            }

            // Xóa cache view
            if ($viewCompiledPath = config('view.compiled')) {
                app('files')->cleanDirectory($viewCompiledPath);
            }
        }

        return $next($request);
    }
}
