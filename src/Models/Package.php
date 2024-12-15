<?php

declare(strict_types=1);

/*
 * @copyright  © 2019 Dxvn, Inc.
 *
 * @author     Tran Ngoc Duc <ductn@diepxuan.com>
 * @author     Tran Ngoc Duc <caothu91@gmail.com>
 *
 * @lastupdate 2024-12-15 12:15:59
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
     * source.
     *
     * @param mixed      $package_name
     * @param null|mixed $path
     */
    public static function source($package_name, $path = null): string
    {
        $packagePath = self::path($package_name, '/src');
        if ($path) {
            $path        = explode(\DIRECTORY_SEPARATOR, trim($path, \DIRECTORY_SEPARATOR));
            $packagePath = explode(\DIRECTORY_SEPARATOR, $packagePath);
            $packagePath = array_merge($packagePath, $path);

            return implode(\DIRECTORY_SEPARATOR, $packagePath);
        }

        return $packagePath;
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

    /**
     * livewireComponentNamespace.
     *
     * @param mixed $package
     */
    public static function livewireComponentNamespace($package): void
    {
        $namespace          = config("{$package}.namespace");
        $package_name       = config("{$package}.name");
        $componentNamespace = "{$namespace}\\Http\\Livewire";

        self::getClassesInNamespace("{$namespace}\\Http\\Livewire")
            ->concat(self::getClassesInDirectory($package_name, '/Http/Livewire'))
            ->unique()
            ->values()
            ->map(static function (string $component) use ($package) {
                $componentName = Str::kebab(class_basename($component));
                Livewire::component("{$package}::{$componentName}", $component);

                return "{$package}::{$componentName}";
            })
            // ->dd()
        ;
    }

    /**
     * getClassesInDirectory.
     *
     * @param mixed $package_name
     * @param mixed $path
     */
    public static function getClassesInDirectory($package_name, $path)
    {
        if (empty($package_name)) {
            return Collection::wrap([]);
        }
        $path = self::source($package_name, $path);
        if (!is_dir($path)) {
            return Collection::wrap([]);
        }

        return Collection::wrap(glob($path . '/*.php'))
            ->map(static fn (string $file): \SplFileInfo => new \SplFileInfo($file))
            ->filter(static fn (\SplFileInfo $file) => $file->isFile())
            ->map(static fn (\SplFileInfo $file) => self::getClassesInFile($file->getRealPath()))
            ->flatten()
        ;
    }

    /**
     * getClassesInFile.
     *
     * @param mixed $filePath
     */
    public static function getClassesInFile($filePath)
    {
        $classes = [];
        if (!file_exists($filePath)) {
            return $classes;
        }

        $content     = file_get_contents($filePath);
        $tokens      = token_get_all($content);
        $namespace   = '';
        $class       = '';
        $isNamespace = false;

        foreach ($tokens as $token) {
            if ($isNamespace && \is_array($token)) {
                $namespace .= $token[1];
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace   = '';
                $isNamespace = true;
            } elseif (';' === $token || '{' === $token) {
                $isNamespace = false;
            }

            if (T_CLASS === $token[0]) {
                $class     = $tokens[array_search($token, $tokens, true) + 2][1];
                $classes[] = trim(ltrim("{$namespace}\\{$class}", '\\'));
            }
        }

        return $classes;
    }

    /**
     * getClassesInNamespace.
     *
     * @param mixed $namespace
     */
    public static function getClassesInNamespace($namespace)
    {
        // $allClasses       = get_declared_classes();
        // $namespaceClasses = array_filter($allClasses, static fn ($class) => Str::startsWith($class, $componentNamespace));
        $composerFile = base_path('vendor/composer/autoload_classmap.php');
        if (!file_exists($composerFile)) {
            return Collection::wrap([]);

            throw new \Exception('Autoload file not found.');
        }
        $namespaces = require $composerFile;
        $classes    = array_keys(array_filter($namespaces, static fn ($path, $class) => str_starts_with($class, $namespace), ARRAY_FILTER_USE_BOTH));

        return Collection::wrap($classes);
    }
}
