<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-05-07 10:41:31
 */

use Composer\InstalledVersions as ComposerPackage;
use Illuminate\Support\Collection;

if (!function_exists('module_path')) {
    function module_path($package_name, $path = null)
    {
        $packagePath = new SplFileInfo(ComposerPackage::getInstallPath($package_name));
        $packagePath = $packagePath->isDir() ? $packagePath : new SplFileInfo(__DIR__ . '/../');

        if ($path) {
            return Str::of($packagePath->getRealPath())
                ->explode(DIRECTORY_SEPARATOR)
                ->push(Str::of($path)->trim()->trim(DIRECTORY_SEPARATOR)->explode(DIRECTORY_SEPARATOR))
                ->flatten()
                ->implode(DIRECTORY_SEPARATOR)
            ;
        }

        return $packagePath->getRealPath();
    }
}

if (!function_exists('module_packages')) {
    /**
     * List packages.
     */
    function module_packages(): Collection
    {
        return Collection::wrap(ComposerPackage::getInstalledPackages())
            ->where(static fn (string $package) => Str::of($package)
                ->startsWith('diepxuan'))
            ->where(static fn (string $package) => !Str::of($package)
                ->is(ComposerPackage::getRootPackage()['name']))
            ->mapWithKeys(static fn (string $package, int $key) => [
                Str::of($package)->afterLast('/')->after('-')->toString() => $package,
            ])
        ;
    }
}
