<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 23:05:59
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    protected $packages;

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        // @todo
        // dd(Diepxuan::test());
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->singleton(Package::class, static fn () => new Package());
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(MigrationServiceProvider::class);
        $this->app->register(TranslationServiceProvider::class);
        $this->app->register(ViewServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
        $this->app->register(MiddlewareServiceProvider::class);
        $this->app->register(RegisterServiceProvider::class);

        $this->registerCommands()
            ->registerCommandSchedules()
        ;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [];
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

    /**
     * Register Commands.
     */
    protected function registerCommands()
    {
        $this->commands([]);

        return $this;
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules()
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });

        return $this;
    }
}
