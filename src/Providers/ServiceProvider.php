<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-05-06 23:00:30
 */

namespace Diepxuan\Providers;

use Composer\InstalledVersions as ComposerPackage;
use Diepxuan\Http\Kernel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;

class ServiceProvider extends BaseServiceProvider
{
    protected string $moduleName      = 'diepxuan/laravel-core';
    protected string $moduleNameLower = 'core';

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        dd($this->packages());

        $this->registerConfig()
            ->registerMiddlewares()
            ->registerCommands()
            ->registerCommandSchedules()
            ->registerViews()
            ->registerTranslations()
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
     * Register translations.
     */
    protected function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }

        return $this;
    }

    /**
     * Register views.
     */
    protected function registerViews()
    {
        $viewPath   = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', $this->moduleName);
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);

        return $this;
    }

    protected function packages(): array
    {
        return Collection::wrap(ComposerPackage::getInstalledPackages())
            ->where(static fn (string $value) => Str::of($value)
                ->startsWith('diepxuan'))
            ->where(static fn (string $value) => !Str::of($value)
                ->is(ComposerPackage::getRootPackage()['name']))
            ->all()
        ;
    }

    /**
     * Register config.
     */
    protected function registerConfig()
    {
        $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);

        return $this;
    }

    /**
     * Register Commands.
     */
    protected function registerCommands()
    {
        // $this->commands([]);

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
        $kernel = new Kernel();
        $kernel->load();

        return $this;
    }

    /**
     * @return array<string>
     */
    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/diepxuan/' . $this->moduleNameLower)) {
                $paths[] = $path . '/diepxuan/' . $this->moduleNameLower;
            }
        }

        return $paths;
    }
}
