<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 22:22:47
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        Package::list()->map(static function (string $package, string $code) {
            if ((new \SplFileInfo(Package::path($package, '/routes/web.php')))->isFile()) {
                Route::middleware('web')->group(Package::path($package, '/routes/web.php'));
            }
            if ((new \SplFileInfo(Package::path($package, '/routes/api.php')))->isFile()) {
                Route::middleware('api')->group(Package::path($package, '/routes/api.php'));
            }

            return $package;
        });
    }
}
