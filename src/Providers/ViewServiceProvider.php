<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2025-05-22 23:24:17
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        Package::list()->map(static function (string $package, string $code): void {
            Package::livewireComponentNamespace($code);
        });
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        Package::list()
            ->each(function (string $package, string $code): void {
                $viewPath   = resource_path('views/modules/' . $code);
                $sourcePath = Package::path($package, 'resources/views');

                $this->publishes([$sourcePath => $viewPath], ['views', $code . '-module-views']);

                $this->loadViewsFrom([$sourcePath, $viewPath], $code);

                $componentNamespace = config("{$code}.namespace");
                Blade::componentNamespace("{$componentNamespace}\\View\\Components", $code);
            })
            ->each(static function (string $package, string $code): void {
                Package::bladeComponentNamespace($code);
                // Package::livewireComponentNamespace($code);
            })
        ;
    }
}
