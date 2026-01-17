# File Module 文件模块

## 目录结构

```
src/File/
├── Core/                           # 核心层
│   ├── Contracts/                  # 接口定义
│   │   └── StorageAdapterInterface.php
│   └── Results/                    # 结果对象
│       ├── FilePathResult.php
│       └── FileUploadResult.php
├── Upload/                         # 文件上传模块
│   ├── Services/
│   │   └── FileUploadService.php
│   ├── Generators/
│   │   └── FilePathGenerator.php
│   └── Options/
│       └── UploadOptions.php
├── Download/                       # 文件下载模块
│   ├── Services/
│   │   └── FileDownloadService.php
│   └── Options/
│       └── DownloadOptions.php
├── Storage/                        # 存储适配器
│   ├── Adapters/
│   │   ├── PublicDiskAdapter.php
│   │   ├── COSDiskAdapter.php
│   │   └── OSSDiskAdapter.php
│   └── Registry/
│       └── StorageAdapterRegistry.php
└── Exceptions/                     # 异常类
    └── FileException.php
```

## 使用方式

### 文件上传
```php
use Valencio\LaravelKit\File\Upload\Services\FileUploadService;
use Valencio\LaravelKit\File\Upload\Options\UploadOptions;

$service = app(FileUploadService::class);
$result = $service->store($file, new UploadOptions(
    disk: 'public',
    prefix: 'uploads'
));
```

### 文件下载
```php
use Valencio\LaravelKit\File\Download\Services\FileDownloadService;
use Valencio\LaravelKit\File\Download\Options\DownloadOptions;

$service = app(FileDownloadService::class);

// 直接下载
$response = $service->download('uploads/file.jpg', new DownloadOptions(
    disk: 'public',
    filename: 'custom-name.jpg'
));

// 获取下载URL
$url = $service->getDownloadUrl('uploads/file.jpg');

// 检查文件是否存在
$exists = $service->exists('uploads/file.jpg', 'public');
```

## 存储适配器

每个适配器都实现了完整的上传和下载功能：

- **PublicDiskAdapter**: 本地公共磁盘
- **COSDiskAdapter**: 腾讯云COS（支持临时URL）
- **OSSDiskAdapter**: 阿里云OSS（支持临时URL）

## 扩展

要添加新的存储驱动，只需：

1. 在 `Storage/Adapters/` 下创建新适配器
2. 实现 `StorageAdapterInterface` 接口
3. 在服务提供者中注册适配器