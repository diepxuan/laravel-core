# Laravel Core

Core package cho DX System.

## Mô tả

Laravel core cho DX System - package nền tảng cung cấp cơ chế auto-discovery và auto-registration cho tất cả packages trong hệ thống.

## 📚 Documentation

- **Project Docs:** [Core Package Loading](../../docs/project/core-package-loading.md) - Auto-discovery system
- **Project Docs:** [Package Namespace Conventions](../../docs/project/package-namespace-conventions.md)

## Chức năng chính

### 1. Package Management
- Model `Package` quản lý danh sách packages từ Composer
- Helper methods: `list()`, `path()`, `getCommands()`, `livewireComponentNamespace()`

### 2. Auto-Discovery System
- Tự động đăng ký **Commands** từ `src/Commands/` của mỗi package
- Tự động đăng ký **Livewire components** từ `src/Http/Livewire/`
- Tự động đăng ký **Blade components** từ `src/View/Components/` và `resources/views/components/`
- Tự động đăng ký **Service Providers** từ `composer.json`

### 3. Core ServiceProviders
- `ConfigServiceProvider` - Merge và publish configs
- `RouteServiceProvider` - Route registration
- `MigrationServiceProvider` - Migration paths
- `TranslationServiceProvider` - Translation paths
- `ViewServiceProvider` - View namespaces
- `MiddlewareServiceProvider` - Middleware registration
- `RegisterServiceProvider` - Additional registrations

### 4. Http Kernel & Middleware
- Custom Http Kernel
- `ClearCache` middleware

### 5. Helpers
- `maskKey()` - Che giấu key nhạy cảm

## Cài đặt / Sử dụng

```bash
composer require diepxuan/laravel-core
```

Package tự động đăng ký ServiceProvider.

## Cấu trúc thư mục

```
laravel-core/
├── src/
│   ├── Http/
│   │   ├── Kernel.php
│   │   └── Middleware/
│   │       └── ClearCache.php
│   ├── Models/
│   │   └── Package.php
│   └── Providers/
│       ├── ServiceProvider.php
│       ├── ConfigServiceProvider.php
│       ├── RouteServiceProvider.php
│       ├── MigrationServiceProvider.php
│       ├── TranslationServiceProvider.php
│       ├── ViewServiceProvider.php
│       ├── MiddlewareServiceProvider.php
│       └── RegisterServiceProvider.php
├── helpers.php
├── docs/
│   └── CORE-PACKAGE-LOADING.md    # Package loading documentation
└── composer.json
```

## Tài liệu

- [Core Package Loading System](docs/CORE-PACKAGE-LOADING.md) - Chi tiết về auto-discovery và config system

## Liên kết đến các package liên quan

Package này là nền tảng cho các package khác trong hệ thống DX:
- laravel-catalog
- laravel-simba
- laravel-support
- laravel-magento
- Và các packages khác đều phụ thuộc vào laravel-core

## Ghi chú

Package core này đóng vai trò trung tâm trong việc điều phối các service và tự động hóa đăng ký commands từ các package con.

**Xem chi tiết:** [docs/CORE-PACKAGE-LOADING.md](docs/CORE-PACKAGE-LOADING.md)