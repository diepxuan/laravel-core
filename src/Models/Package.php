<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-07-04 21:53:35
 */

namespace Diepxuan\Core\Models;

use Composer\InstalledVersions as ComposerPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Package
{
    /**
     * List diepxuan packages.
     */
    public static function list(): Collection
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

    /**
     * path.
     *
     * @param mixed $package_name
     * @param mixed $path
     */
    public static function path($package_name, $path = null)
    {
        $packagePath = ComposerPackage::getInstallPath($package_name);
        $packagePath = $packagePath ?: base_path($package_name);
        $packagePath = new \SplFileInfo($packagePath);
        $packagePath = $packagePath->isDir() ? $packagePath : new \SplFileInfo(__DIR__ . '/../');

        if ($path) {
            $path        = explode(\DIRECTORY_SEPARATOR, trim($path, \DIRECTORY_SEPARATOR));
            $packagePath = explode(\DIRECTORY_SEPARATOR, $packagePath->getRealPath());
            $packagePath = array_merge($packagePath, $path);

            return implode(\DIRECTORY_SEPARATOR, $packagePath);
        }

        return $packagePath->getRealPath();
    }
}
