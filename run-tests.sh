#!/bin/bash

# Run tests for laravel-core package
# This script runs from the package directory but bootstraps the main Laravel app

cd "$(dirname "$0")/../.."  # Go to portal root

echo "=== Running laravel-core tests ==="
echo "Current directory: $(pwd)"

# Run PHPUnit with the package's phpunit.xml
./vendor/bin/phpunit -c diepxuan/laravel-core/phpunit.xml "$@"