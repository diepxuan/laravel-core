<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-12-13 08:39:57
 */

namespace Diepxuan\Core\Models;

use Composer\InstalledVersions as ComposerPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Livewire;

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
    public static function path($package_name, $path = null): string
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

    public static function livewireComponentNamespace($package): void
    {
        $namespace          = config("{$package}.namespace");
        $componentNamespace = "{$namespace}\\Http\\Livewire";
        // dd(Livewire::componentNamespace("{$componentNamespace}\\Http\\Livewire", $code));

        // $allClasses       = get_declared_classes();
        // $namespaceClasses = array_filter($allClasses, static fn ($class) => Str::startsWith($class, $componentNamespace));

        // Package::getClassesInNamespace($componentNamespace);
        foreach (self::getClassesInNamespace("{$componentNamespace}") as $component) {
            $componentName = Str::kebab(class_basename($component));
            Livewire::component("{$package}::{$componentName}", $component);
        }

        // $classmap = require base_path('vendor/composer/autoload_classmap.php');

        // $classes = array_keys(array_filter($classmap, static fn ($path, $class) => str_starts_with($class, $componentNamespace), ARRAY_FILTER_USE_BOTH));

        // dd($componentNamespace, $classes);
        // Livewire::componentNamespace("{$componentNamespace}\\Http\\Livewire", $code);
    }

    public static function getClassesInNamespace($namespace)
    {
        $composerFile = base_path('vendor/composer/autoload_classmap.php');
        if (!file_exists($composerFile)) {
            return[];

            throw new \Exception('Autoload file not found.');
        }
        $namespaces = require $composerFile;
        $classes    = array_keys(array_filter($namespaces, static fn ($path, $class) => str_starts_with($class, $namespace), ARRAY_FILTER_USE_BOTH));

        return $classes;
    }

    public static function getClassesInDirectory($path)
    {
        if ((new \SplFileInfo(self::path($path, '/config/config.php')))->isFile()) {
            $this->publishes([self::path($package, 'config/config.php') => config_path($code . '.php')], 'config');
            $this->mergeConfigFrom(self::path($package, 'config/config.php'), $code);
        }

        // Tự động quét thư mục Livewire components
        $componentPath = __DIR__ . '/../resources/views/livewire';

        if (is_dir($path)) {
            foreach (glob($path . '/*.php') as $file) {
                $componentName = basename($file, '.php');
                Livewire::component($componentName, "Vendor\\PackageName\\Http\\Livewire\\{$componentName}");
            }
        }

        return $classes;
    }
}
