<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  FilePathGenerator.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File;

use Illuminate\Http\UploadedFile;
use Random\RandomException;

/**
 * 文件路径生成器类
 * 用于生成和管理文件路径的工具类
 */
class FilePathGenerator
{
    /**
     * 生成上传文件的路径信息
     *
     * @param UploadedFile $file 上传的文件对象
     * @param string|null $appName 应用名称，用于路径组织
     * @param string $prefix 路径前缀，默认为'uploads'
     * @param string $dateFormat 日期格式，默认为'Ymd'
     * @param string $namingStrategy 命名策略，默认为'random'
     * @return FilePathResult 包含文件路径信息的结果对象
     * @throws RandomException
     */
    public function generate (
        UploadedFile $file,
        ?string      $appName = null,
        string       $prefix = 'uploads',
        string       $dateFormat = 'Ymd',
        string       $namingStrategy = 'random'
    ): FilePathResult {
        // 解析文件扩展名
        $extension = $this->resolveExtension($file);

        // 生成文件目录路径
        $directory = $this->generateDirectory(
            appName: $appName,
            prefix: $prefix,
            dateFormat: $dateFormat
        );

        // 生成文件名
        $filename = $this->generateFilename(
            file: $file,
            extension: $extension,
            strategy: $namingStrategy
        );

        return new FilePathResult(
            directory: $directory,
            filename: $filename,
            extension: $extension,
            namingStrategy: $namingStrategy
        );
    }


    /**
     * @param UploadedFile $file
     * @return string
     */
    private function resolveExtension (UploadedFile $file): string
    {
        return strtolower($file->getClientOriginalExtension() ?: 'bin');
    }


    /**
     * 生成目录路径
     *
     * 根据应用名称、前缀和日期格式生成一个完整的目录路径字符串
     *
     * @param string|null $appName 应用名称，用于路径的第一部分（可选）
     * @param string $prefix 目录前缀，用于路径的第二部分，默认为'uploads'
     * @param string $dateFormat 日期格式，用于生成日期目录部分
     * @return string 生成的目录路径，使用'/'作为路径分隔符
     */
    private function generateDirectory (?string $appName, string $prefix, string $dateFormat): string
    {
        $parts = [];

        $appName = $this->sanitizePathSegment($appName);
        if ($appName !== null && $appName !== '') {
            $parts[] = $appName;
        }

        $prefix = $this->sanitizePathSegment($prefix) ?? 'uploads';
        $parts[] = $prefix;

        $parts[] = date($dateFormat);

        return implode('/', $parts);
    }


    /**
     * 生成上传文件的文件名
     *
     * @param UploadedFile $file 上传的文件对象
     * @param string $extension 文件扩展名
     * @param string $strategy 文件名生成策略 (md5, sha1, sha256, original, random)
     * @return string 生成的完整文件名（包含扩展名）
     * @throws RandomException
     */
    private function generateFilename (UploadedFile $file, string $extension, string $strategy): string
    {
        $strategy = strtolower(trim($strategy));

        // 根据策略生成基础文件名
        $baseName = match ($strategy) {
            'md5' => $this->hashName($file, 'md5'),
            'sha1' => $this->hashName($file, 'sha1'),
            'sha256' => $this->hashName($file, 'sha256'),
            'original' => $this->originalName($file),
            default => $this->randomName(),
        };

        return $baseName . '.' . $extension;
    }


    /**
     * 生成一个随机名称
     *
     * 该方法使用加密安全的随机字节生成器创建一个16字节的随机字符串，
     * 然后将其转换为十六进制格式，生成一个32字符的随机名称。
     *
     * @return string 返回一个32字符的十六进制随机字符串
     * @throws RandomException
     */
    private function randomName (): string
    {
        return bin2hex(random_bytes(16));
    }


    /**
     * 生成文件的哈希名称
     *
     * 此方法接收一个已上传的文件对象和一个哈希算法作为参数，使用指定的哈希算法计算该文件内容的哈希值。
     * 如果文件的实际路径无法获取，则返回一个随机生成的名字。
     *
     * @param UploadedFile $file 已上传的文件对象
     * @param string $algo 用于计算哈希值的算法
     * @return string 返回基于文件内容的哈希字符串，如果文件路径无效则返回一个随机字符串
     * @throws RandomException
     */
    private function hashName (UploadedFile $file, string $algo): string
    {
        $path = $file->getRealPath();
        if ($path === false || $path === '') {
            return $this->randomName();
        }

        return hash_file($algo, $path);
    }


    /**
     * 获取上传文件的原始文件名（不包含扩展名），并进行格式化处理
     *
     * 该方法会提取文件的原始名称，移除扩展名，然后将非字母数字下划线和连字符的字符替换为下划线
     * 如果处理后的名称为空，则返回一个随机名称
     *
     * @param UploadedFile $file 上传的文件对象
     * @return string 格式化后的原始文件名，如果原文件名无效则返回随机名称
     * @throws RandomException
     */
    private function originalName (UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name) ?: '';

        return $name !== '' ? $name : $this->randomName();
    }


    /**
     * 清理路径中的无效字符
     *
     * @param string|null $value 要清理的字符串
     * @return string|null 清理后的字符串，如果输入为空则返回 null
     */
    private function sanitizePathSegment (?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value, "/ \t\n\r\0\x0B");
        if ($value === '') {
            return null;
        }

        // 只允许字母数字、下划线、中横线
        $value = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $value);

        // 防止出现空串
        return $value !== '' ? $value : null;
    }
}
