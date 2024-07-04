<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 23:41:07
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class RegisterServiceProvider extends ServiceProvider
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    protected $fileSystem;

    /**
     * Create a new service provider instance.
     *
     * @param Application $app
     */
    public function __construct($app)
    {
        $this->app        = $app;
        $this->fileSystem = new Filesystem();
    }

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
        Package::list()->where(static fn (string $package) => !Str::of($package)
            ->is('diepxuan/laravel-core'))
            ->map(function (string $package, string $code) {
                $composer = new \SplFileInfo(Package::path($package, '/composer.json'));
                if (!$composer->isFile()) {
                    return;
                }
                $composer  = json_decode($this->fileSystem->get($composer->getRealPath()), true);
                $providers = $composer['extra']['laravel']['providers'] ?? [];

                return Collection::wrap($providers)->map(function (string $provider, string $code) {
                    if (!class_exists($provider)) {
                        return;
                    }
                    $this->app->register($provider);

                    return $provider;
                });
            })
        ;
    }
}
