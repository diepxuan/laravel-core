<?php

namespace Diepxuan\Core\Tests\Unit;

use Diepxuan\Core\Models\Package;
use Diepxuan\Core\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class PackageConfigTest extends TestCase
{
    /**
     * Test that Package::list() returns all diepxuan packages.
     */
    public function test_package_list_returns_diepxuan_packages()
    {
        $packages = Package::list();
        
        $this->assertNotEmpty($packages, 'Package::list() should return non-empty collection');
        
        // Check that we have expected packages
        $expectedPackages = ['catalog', 'core', 'disreg', 'gmail', 'log', 'magento', 'ronaldjack', 'scavenger', 'simba', 'support'];
        
        foreach ($expectedPackages as $expectedPackage) {
            $this->assertArrayHasKey(
                $expectedPackage,
                $packages->toArray(),
                "Package '{$expectedPackage}' should be in Package::list()"
            );
        }
    }

    /**
     * Test that config is loaded for each package.
     */
    public function test_config_is_loaded_for_each_package()
    {
        $packages = Package::list();
        
        foreach ($packages as $code => $packageName) {
            // Test namespace config
            $namespaceKey = "{$code}.namespace";
            $namespace = config($namespaceKey);
            
            $this->assertNotNull(
                $namespace,
                "Config key '{$namespaceKey}' should not be null for package '{$code}'"
            );
            
            $this->assertIsString(
                $namespace,
                "Config key '{$namespaceKey}' should be a string for package '{$code}'"
            );
            
            // Test name config
            $nameKey = "{$code}.name";
            $name = config($nameKey);
            
            $this->assertNotNull(
                $name,
                "Config key '{$nameKey}' should not be null for package '{$code}'"
            );
            
            $this->assertStringContainsString(
                'diepxuan/laravel-',
                $name,
                "Package name should contain 'diepxuan/laravel-' for package '{$code}'"
            );
        }
    }

    /**
     * Test that Package::getCommands() works for each package.
     */
    public function test_package_get_commands_returns_valid_commands()
    {
        $packages = Package::list();
        
        foreach ($packages as $code => $packageName) {
            $commands = Package::getCommands($code);
            
            $this->assertNotNull(
                $commands,
                "Package::getCommands('{$code}') should not return null"
            );
            
            // Some packages may not have commands, that's okay
            if ($commands->isNotEmpty()) {
                foreach ($commands as $commandClass) {
                    $this->assertTrue(
                        class_exists($commandClass),
                        "Command class '{$commandClass}' should exist for package '{$code}'"
                    );
                    
                    // Check if it's a subclass of Command (but don't require it for all)
                    // Some might be other types of classes in Commands directory
                }
            }
        }
    }

    /**
     * Test backward compatibility with config.php files.
     */
    public function test_backward_compatibility_with_config_php_files()
    {
        // This test ensures that if a package has config.php instead of {code}.php,
        // it still works
        
        $packages = Package::list();
        
        foreach ($packages as $code => $packageName) {
            $configPathNew = Package::path($packageName, "/config/{$code}.php");
            $configPathLegacy = Package::path($packageName, '/config/config.php');
            
            // At least one config file should exist
            $configExists = file_exists($configPathNew) || file_exists($configPathLegacy);
            
            $this->assertTrue(
                $configExists,
                "Package '{$code}' should have either {$code}.php or config.php config file"
            );
            
            // If new convention file exists, it should have correct structure
            if (file_exists($configPathNew)) {
                $config = include $configPathNew;
                
                $this->assertArrayHasKey(
                    'name',
                    $config,
                    "Config file {$code}.php should have 'name' key"
                );
                
                $this->assertArrayHasKey(
                    'namespace',
                    $config,
                    "Config file {$code}.php should have 'namespace' key"
                );
                
                $this->assertIsString(
                    $config['namespace'],
                    "Namespace in {$code}.php should be a string (not Class::class)"
                );
            }
        }
    }

    /**
     * Test that config keys match package codes.
     */
    public function test_config_keys_match_package_codes()
    {
        $packages = Package::list();
        
        foreach ($packages as $code => $packageName) {
            // These config keys should exist for each package
            $configKeys = [
                "{$code}.namespace",
                "{$code}.name",
            ];
            
            foreach ($configKeys as $configKey) {
                $value = config($configKey);
                
                $this->assertNotNull(
                    $value,
                    "Config key '{$configKey}' should exist for package '{$code}'"
                );
            }
        }
    }

    /**
     * Test that package namespace follows convention.
     */
    public function test_package_namespace_follows_convention()
    {
        $packages = Package::list();
        
        foreach ($packages as $code => $packageName) {
            $namespace = config("{$code}.namespace");
            
            // Namespace should start with Diepxuan\
            $this->assertStringStartsWith(
                'Diepxuan\\',
                $namespace,
                "Namespace for package '{$code}' should start with 'Diepxuan\\'"
            );
            
            // Convert code to StudlyCase for comparison
            $expectedStudlyCase = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $code)));
            
            // Namespace should contain the package name in StudlyCase
            // This is not a strict requirement but a convention check
            $this->assertStringContainsString(
                $expectedStudlyCase,
                $namespace,
                "Namespace for package '{$code}' should contain package name in StudlyCase"
            );
        }
    }
}