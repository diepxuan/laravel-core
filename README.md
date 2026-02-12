# Laravel Core

Core package cho DX System.

## Mô tả

Laravel core cho DX System (lấy từ composer.json).

## Chức năng chính

- Cung cấp các ServiceProvider cốt lõi: Config, Route, Migration, Translation, View, Middleware, Register.
- Quản lý package thông qua model Package.
- Tự động đăng ký commands và schedules từ các package được cài đặt.
- Cung cấp các middleware (ClearCache) và Http Kernel.
- Cung cấp helper `maskKey` để che giấu key nhạy cảm.

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
└── composer.json
```

## Liên kết đến các package liên quan

Package này là nền tảng cho các package khác trong hệ thống DX.

## Ghi chú

Package core này đóng vai trò trung tâm trong việc điều phối các service và tự động hóa đăng ký commands từ các package con.