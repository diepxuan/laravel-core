<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 16:38:12
 */

use Composer\InstalledVersions as ComposerPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

if (!function_exists('module_path')) {
    function module_path($package_name, $path = null)
    {
        $packagePath = ComposerPackage::getInstallPath($package_name);
        $packagePath = $packagePath ?: base_path($package_name);
        $packagePath = new SplFileInfo($packagePath);
        $packagePath = $packagePath->isDir() ? $packagePath : new SplFileInfo(__DIR__ . '/../');

        if ($path) {
            $path        = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR));
            $packagePath = explode(DIRECTORY_SEPARATOR, $packagePath->getRealPath());
            $packagePath = array_merge($packagePath, $path);

            return implode(DIRECTORY_SEPARATOR, $packagePath);
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
