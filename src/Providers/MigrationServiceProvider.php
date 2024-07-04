<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 22:35:29
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Support\ServiceProvider;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        // parent::boot();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        Package::list()->map(function (string $package, string $code): void {
            $this->loadMigrationsFrom(Package::path($package, 'database/migrations'));
        });
    }
}
