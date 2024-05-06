<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-05-06 15:53:22
 */

use Composer\InstalledVersions as ComposerPackage;

if (!function_exists('module_path')) {
    function module_path($package_name, $path = null)
    {
        $packagePath = new SplFileInfo(ComposerPackage::getInstallPath($package_name));
        $packagePath = $packagePath->isDir() ? $packagePath : new SplFileInfo(__DIR__ . '/../');

        if ($path) {
            return Str::of($packagePath->getRealPath())
                ->explode(DIRECTORY_SEPARATOR)
                ->push(Str::of($path)->trim()->explode(DIRECTORY_SEPARATOR))
                ->flatten()
                ->implode(DIRECTORY_SEPARATOR)
            ;
        }

        return $packagePath->getRealPath();
    }
}
