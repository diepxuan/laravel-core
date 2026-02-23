<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2025-04-22 15:16:33
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
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
            // Try to load config file with package name first (new convention)
            $configFile = Package::path($package, "/config/{$code}.php");
            $configFileInfo = new \SplFileInfo($configFile);
            
            // Fallback to config.php for backward compatibility
            if (!$configFileInfo->isFile()) {
                $configFile = Package::path($package, '/config/config.php');
                $configFileInfo = new \SplFileInfo($configFile);
            }
            
            if ($configFileInfo->isFile()) {
                $this->publishes([$configFile => config_path($code . '.php')], 'config');
                $this->mergeConfigFrom($configFile, $code);
            }
        });
    }
}
