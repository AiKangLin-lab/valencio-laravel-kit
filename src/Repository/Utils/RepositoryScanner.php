<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  RepositoryScanner.php
// +----------------------------------------------------------------------
// | Year:      2025/8/18/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Repository\Utils;

use FilesystemIterator;

/**
 *
 */
class RepositoryScanner
{
    /**
     * 扫描指定基路径下的两级目录
     *
     * @param string $basePath
     * @return array<string, string>  [接口 => 实现]
     */
    public static function scan (string $basePath): array
    {
        return self::scanTwoLevelDirectories($basePath);
    }

    /**
     * 核心：扫描两层目录，收集所有 Repository 绑定
     *
     * @param string $basePath
     * @return array<string, string>
     */
    private static function scanTwoLevelDirectories (string $basePath): array
    {
        $bindings = [];

        // 遍历一级模块
        foreach (new FilesystemIterator($basePath, FilesystemIterator::SKIP_DOTS) as $moduleInfo) {
            if (!$moduleInfo->isDir()) {
                continue;
            }

            $module = $moduleInfo->getFilename();
            $modulePath = $moduleInfo->getPathname();
            $moduleNs = "App\\Services\\Apps\\{$module}";

            // 扫描一级目录（例如：Member、Order）
            $bindings += self::scanDirectory($modulePath, $moduleNs);

            // 遍历二级子模块（例如：Parking/PlateBinding）
            foreach (new FilesystemIterator($modulePath, FilesystemIterator::SKIP_DOTS) as $subModuleInfo) {
                if (!$subModuleInfo->isDir()) {
                    continue;
                }

                $subModule = $subModuleInfo->getFilename();
                $subModulePath = $subModuleInfo->getPathname();
                $subModuleNs = "{$moduleNs}\\{$subModule}";

                $bindings += self::scanDirectory($subModulePath, $subModuleNs);
            }
        }

        return $bindings;
    }

    /**
     * 扫描单个目录，收集 Contracts / Repository 下的绑定关系
     *
     * @param string $path
     * @param string $namespace
     * @return array<string, string>
     */
    private static function scanDirectory (string $path, string $namespace): array
    {
        $bindings = [];

        $contractsPath = "{$path}/Contracts";
        $repositoryPath = "{$path}/Repository";

        if (!is_dir($contractsPath) || !is_dir($repositoryPath)) {
            return $bindings;
        }

        // 遍历所有 Contracts 下的 *RepositoryInterface.php
        foreach (glob("{$contractsPath}/*RepositoryInterface.php") as $contractFile) {
            $name = basename($contractFile, "RepositoryInterface.php");
            $interface = "{$namespace}\\Contracts\\{$name}RepositoryInterface";
            $repoClass = "{$namespace}\\Repository\\{$name}Repository";

            if (interface_exists($interface) && class_exists($repoClass)) {
                $bindings[$interface] = $repoClass;
            }
        }

        return $bindings;
    }
}
