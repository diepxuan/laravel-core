# Package Namespace Conventions

## 🎯 Tổng quan

Tài liệu này ghi lại các quy ước namespace cho tất cả packages trong hệ thống. **QUAN TRỌNG**: Các AI agent sau phải tuân thủ các quy ước này để tránh phá hủy hệ thống.

## ⚠️ QUY TẮC CHUNG

### 1. **Config Namespace ≠ Package Name**
- **Package name**: `diepxuan/laravel-{name}` (trong composer.json)
- **Config namespace**: Có thể khác với package name
- **Code namespace**: Phải match với config namespace

### 2. **Case Sensitivity**
- PHP namespaces là **case-sensitive**
- Phải đúng chính xác chữ hoa/chữ thường

### 3. **Config File Naming**
- Config file: `{package-code}.php` (e.g., `disreg.php`, `support.php`)
- Config key: `{package-code}` (e.g., `config('disreg.namespace')`)

## 📋 DANH SÁCH PACKAGES VÀ NAMESPACE CONVENTIONS

### 1. **laravel-disreg** - ⚠️ QUAN TRỌNG
```
Package:    diepxuan/laravel-disreg
Config key: disreg
Config file: config/disreg.php
Config namespace: Diepxuan\DisableRegistration  ← DÙNG CÁI NÀY!
Code namespace: Diepxuan\DisableRegistration\...
```

**LƯU Ý QUAN TRỌNG**:
- **KHÔNG DÙNG**: `Diepxuan\Disreg` (sai)
- **PHẢI DÙNG**: `Diepxuan\DisableRegistration` (đúng)
- **Lý do**: Code đã tồn tại với namespace `DisableRegistration`

### 2. **laravel-ronaldjack** - ⚠️ CASE SENSITIVE
```
Package:    diepxuan/laravel-ronaldjack
Config key: ronaldjack
Config file: config/ronaldjack.php
Config namespace: Diepxuan\RonaldJack  ← Chú ý 'J' viết hoa!
Code namespace: Diepxuan\RonaldJack\...
```

**LƯU Ý**:
- **KHÔNG DÙNG**: `Diepxuan\Ronaldjack` (j thường)
- **PHẢI DÙNG**: `Diepxuan\RonaldJack` (J viết hoa)

### 3. **laravel-catalog** - ✅ Chuẩn
```
Package:    diepxuan/laravel-catalog
Config key: catalog
Config file: config/catalog.php
Config namespace: Diepxuan\Catalog
Code namespace: Diepxuan\Catalog\...
```

### 4. **laravel-core** - ✅ Chuẩn
```
Package:    diepxuan/laravel-core
Config key: core
Config file: config/core.php
Config namespace: Diepxuan\Core
Code namespace: Diepxuan\Core\...
```

### 5. **laravel-support** - ✅ Chuẩn
```
Package:    diepxuan/laravel-support
Config key: support
Config file: config/support.php
Config namespace: Diepxuan\Support
Code namespace: Diepxuan\Support\...
```

### 6. **laravel-simba** - ✅ Chuẩn
```
Package:    diepxuan/laravel-simba
Config key: simba
Config file: config/simba.php
Config namespace: Diepxuan\Simba
Code namespace: Diepxuan\Simba\...
```

### 7. **laravel-magento** - ✅ Chuẩn
```
Package:    diepxuan/laravel-magento
Config key: magento
Config file: config/magento.php
Config namespace: Diepxuan\Magento
Code namespace: Diepxuan\Magento\...
```

### 8. **laravel-log** - ✅ Chuẩn
```
Package:    diepxuan/laravel-log
Config key: log
Config file: config/log.php
Config namespace: Diepxuan\Log
Code namespace: Diepxuan\Log\...
```

### 9. **Empty Packages (Chỉ config)**
Các packages sau chỉ có config files, không có source code:
- **laravel-gmail**: `Diepxuan\Gmail` (chỉ config)
- **laravel-scavenger**: `Diepxuan\Scavenger` (chỉ config + helpers)

### 10. **Packages không có config files**
- `diepxuan/laravel-currency`
- `diepxuan/laravel-eloquent-composite`
- `diepxuan/php-charset`

## 🔧 CÁCH KIỂM TRA NAMESPACE

### 1. Kiểm tra config namespace:
```bash
# Xem config namespace của package
php -r "require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo config('disreg.namespace');"
```

### 2. Kiểm tra code namespace:
```bash
# Xem namespace thực tế trong source code
find diepxuan/laravel-disreg/src -name "*.php" -exec grep -h "^namespace" {} \; | head -1
```

### 3. Verify với Package system:
```bash
php test_config_loading.php
```

## 🚨 CÁC LỖI THƯỜNG GẶP

### Lỗi 1: Namespace mismatch
**Triệu chứng**: `Package::getCommands()` không tìm thấy commands
**Nguyên nhân**: Config namespace ≠ Code namespace
**Ví dụ**: `disreg` dùng `Diepxuan\Disreg` thay vì `Diepxuan\DisableRegistration`

### Lỗi 2: Case sensitivity
**Triệu chứng**: Class không found
**Nguyên nhân**: `Ronaldjack` vs `RonaldJack`
**Fix**: Đúng chính xác case

### Lỗi 3: Missing config file
**Triệu chứng**: `config('support.namespace')` trả về null
**Nguyên nhân**: Thiếu config/support.php
**Fix**: Tạo config file đúng tên

## 📝 WORKFLOW KHI LÀM VIỆC VỚI PACKAGES

### Bước 1: Xác định package code
```bash
# Xem package có trong hệ thống không
php -r "require 'vendor/autoload.php'; use Diepxuan\Core\Models\Package; print_r(Package::list()->toArray());"
```

### Bước 2: Kiểm tra config hiện tại
```bash
# Xem config namespace
echo "config('disreg.namespace'): " . config('disreg.namespace');
```

### Bước 3: Kiểm tra code namespace
```bash
# Xem namespace thực tế trong code
grep -r "namespace" diepxuan/laravel-disreg/src/ --include="*.php" | head -1
```

### Bước 4: Đảm bảo consistency
- Config namespace phải match code namespace
- Case phải chính xác
- Config file phải tồn tại

## 🏗️ ARCHITECTURE NOTES

### Tại sao có sự khác biệt?
1. **Historical reasons**: Một số packages được tạo trước khi có convention
2. **Descriptive names**: `DisableRegistration` mô tả rõ hơn `Disreg`
3. **Backward compatibility**: Giữ nguyên code đã tồn tại

### Core Package System Dependency:
```
Package::getCommands('disreg')
    → config('disreg.namespace')  # Must return 'Diepxuan\DisableRegistration'
    → Look for: Diepxuan\DisableRegistration\Commands\*
    → Auto-register commands
```

## ✅ CHECKLIST TRƯỚC KHI THAY ĐỔI

- [ ] Đã kiểm tra config namespace hiện tại
- [ ] Đã kiểm tra code namespace thực tế
- [ ] Đã verify case sensitivity
- [ ] Đã test `Package::getCommands()`
- [ ] Đã test `config('{package}.namespace')`
- [ ] Đã cập nhật documentation nếu cần

## 🆘 KHI GẶP VẤN ĐỀ

1. **Consult this document** đầu tiên
2. **Check actual code** để xem namespace thực tế
3. **Test with Package system** để verify
4. **Update documentation** nếu phát hiện inconsistency mới

---

**Lưu ý cuối cùng**: Hệ thống package namespace là nền tảng của auto-discovery. Mọi thay đổi phải được test kỹ và document đầy đủ cho các AI agent sau.
