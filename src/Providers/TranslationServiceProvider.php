<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2025-04-22 15:20:54
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
            $langPath = resource_path('lang/modules/' . $code);

            if (is_dir($langPath)) {
                $this->loadTranslationsFrom($langPath, $code);
                $this->loadJsonTranslationsFrom($langPath);
            } else {
                $this->loadTranslationsFrom(Package::path($package, 'lang'), $code);
                $this->loadJsonTranslationsFrom(Package::path($package, 'lang'));
            }
        });
    }
}
