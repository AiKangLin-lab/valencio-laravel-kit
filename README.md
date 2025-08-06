# Valencio Laravel Kit

一个高扩展性、可插拔的 Laravel 辅助开发包，支持多模块（如上传、XX模块等），适合自用或团队协作。

## 安装

```shell
composer require valenciokang/laravel-kit-upload
```

## 发布配置和语言包

> 本包支持按模块选择性发布配置文件，语言包一次性全部发布。

### 发布上传模块配置
```shell
php artisan vendor:publish --tag=kit-upload-config
```

### 发布 XX 模块配置（如有新增模块）
```shell
php artisan vendor:publish --tag=kit-xx-config
```

### 发布所有语言包
```shell
php artisan vendor:publish --tag=kit-lang
```

### 一次性发布所有资源
```shell
php artisan vendor:publish --provider="Valencio\LaravelKit\Providers\KitServiceProvider"
```

## 配置说明

- 所有配置文件位于 `config/kit/` 目录下（如 `upload.php`、`xx.php`）。
- 只需发布你需要的模块配置即可。
- 语言包位于 `resources/lang/vendor/kit/`，支持多语言。

## 用法示例（以上传为例）

```php
use Valencio\LaravelKit\Upload\UploadManager;

// 依赖注入或 app() 获取
$manager = app(UploadManager::class);

// 上传文件（自动命名）
$path = $manager->store($request->file('file'));

// 上传文件（自定义文件名）
$path = $manager->store($request->file('file'), null, 'default', 'myfile.jpg');
```

## 扩展模块

- 新增模块时，添加对应的配置文件和 publishes 行即可。
- 实现自己的功能类，按需注册到 ServiceProvider。

## 其他说明

- 遵循 PSR-4 自动加载规范
- 适合自用或团队内部 Composer 包管理

## License

MIT