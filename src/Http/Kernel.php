<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2026-02-26 23:11:06
 */

namespace Diepxuan\Core\Http;

use Diepxuan\Core\Http\Middleware\ClearCache;
use Diepxuan\Core\Http\Middleware\TrustProxies;

class Kernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'clearcache' => ClearCache::class,
    ];

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * Load middlewares.
     */
    public function load(): void
    {
        foreach ($this->middlewareAliases as $alias => $middleware) {
            app()->make('router')->aliasMiddleware($alias, $middleware);
        }
        foreach ($this->middleware as $middleware) {
            app()->make(\Illuminate\Contracts\Http\Kernel::class)->pushMiddleware($middleware);
        }
    }
}
