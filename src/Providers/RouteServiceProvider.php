<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-05-07 10:59:16
 */

namespace Diepxuan\Providers;

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
        $self = $this;
        $this->packages()->map(static function (string $package, string $code) use (&$self) {
            // Route::prefix($code)->group(static function () use ($package): void {
            if ((new \SplFileInfo(module_path($package, '/routes/web.php')))->isFile()) {
                Route::middleware('web')->group(module_path($package, '/routes/web.php'));
            }
            if ((new \SplFileInfo(module_path($package, '/routes/api.php')))->isFile()) {
                Route::middleware('api')->group(module_path($package, '/routes/api.php'));
            }
            // });

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
        $this->packages = module_packages();

        return $this->packages;
    }
}
