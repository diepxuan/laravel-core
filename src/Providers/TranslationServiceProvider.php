<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2025-05-31 13:47:02
 */

namespace Diepxuan\Core\Providers;

use Diepxuan\Core\Models\Package;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
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
            if (is_dir($langPath = resource_path('lang/modules/' . $code))) {
                $this->loadTranslationsFrom($langPath);
                $this->loadTranslationsFrom($langPath, $code);
                $this->loadJsonTranslationsFrom($langPath);
            }
            if (is_dir($langPath = Package::resource_path($package, 'lang'))) {
                $this->loadTranslationsFrom($langPath);
                $this->loadTranslationsFrom($langPath, $code);
                $this->loadJsonTranslationsFrom($langPath);
            }
        });
    }
}
