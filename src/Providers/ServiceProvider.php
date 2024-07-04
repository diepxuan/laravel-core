<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 22:01:52
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Http\Kernel;
use Diepxuan\Core\Models\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
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

        $this->registerConfig()
            ->registerMiddlewares()
            ->registerCommands()
            ->registerCommandSchedules()
            ->registerViews()
            ->registerTranslations()
            ->registerMigrations()
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
     * registerMigrations.
     */
    protected function registerMigrations()
    {
        $self = $this;
        $this->packages()->map(static function (string $package, string $code) use (&$self) {
            $self->loadMigrationsFrom(Package::path($package, 'database/migrations'));

            return $package;
        });

        return $this;
    }

    /**
     * Register translations.
     */
    protected function registerTranslations()
    {
        $self = $this;
        $this->packages()->map(static function (string $package, string $code) use (&$self) {
            $langPath = resource_path('lang/modules/' . $code);

            if (is_dir($langPath)) {
                $self->loadTranslationsFrom($langPath, $code);
                $self->loadJsonTranslationsFrom($langPath);
            } else {
                $self->loadTranslationsFrom(Package::path($package, 'lang'), $code);
                $self->loadJsonTranslationsFrom(Package::path($package, 'lang'));
            }

            return $package;
        });

        return $this;
    }

    /**
     * Register views.
     */
    protected function registerViews()
    {
        $self = $this;
        $this->packages()->map(static function (string $package, string $code) use (&$self) {
            $viewPath   = resource_path('views/modules/' . $code);
            $sourcePath = Package::path($package, 'resources/views');

            $self->publishes([$sourcePath => $viewPath], ['views', $code . '-module-views']);

            $self->loadViewsFrom(array_merge($self->getPublishableViewPaths($code), [$sourcePath]), $code);

            $componentNamespace = str_replace('/', '\\', $package);
            Blade::componentNamespace($componentNamespace, $code);

            return $package;
        });

        return $this;
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
     * Register config.
     */
    protected function registerConfig()
    {
        $this->packages()->map(function (string $package, string $code) {
            if ((new \SplFileInfo(Package::path($package, '/config/config.php')))->isFile()) {
                $this->publishes([Package::path($package, 'config/config.php') => config_path($code . '.php')], 'config');
                $this->mergeConfigFrom(Package::path($package, 'config/config.php'), $code);
            }

            return $package;
        });

        return $this;
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

    /**
     * Register Middlewares.
     */
    protected function registerMiddlewares()
    {
        new Kernel();

        return $this;
    }

    /**
     * @param mixed $moduleCode
     *
     * @return array<string>
     */
    private function getPublishableViewPaths($moduleCode = ''): array
    {
        $paths      = [];
        $moduleCode = "/{$moduleCode}";
        foreach (config('view.paths') as $path) {
            if (is_dir("{$path}/diepxuan{$moduleCode}")) {
                $paths[] = "{$path}/diepxuan{$moduleCode}";
            }
        }

        return $paths;
    }
}
