<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 21:56:08
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $packages;

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
        $this->packages()->map(static function (string $package, string $code) {
            if ((new \SplFileInfo(Package::path($package, '/routes/web.php')))->isFile()) {
                Route::middleware('web')->group(Package::path($package, '/routes/web.php'));
            }
            if ((new \SplFileInfo(Package::path($package, '/routes/api.php')))->isFile()) {
                Route::middleware('api')->group(Package::path($package, '/routes/api.php'));
            }

            return $package;
        });
    }

    /**
     * List packages.
     */
    protected function packages(): Collection
    {
        if ($this->packages) {
            return $this->packages;
        }
        $this->packages = Package::list();

        return $this->packages;
    }
}
