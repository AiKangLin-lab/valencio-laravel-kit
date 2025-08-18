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
     * @param string $path
     * @return array
     */
    public static function scan(string $path): array
    {
        $bindings = [];
        foreach (new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS) as $moduleInfo) {
            if (!$moduleInfo->isDir()) continue;


            $module     = $moduleInfo->getFilename();
            $modulePath = $moduleInfo->getPathname();
            $moduleNs   = "App\\Services\\Apps\\{$module}";

            if (is_dir("{$modulePath}/Repository") && is_dir("{$modulePath}/Contracts")) {
                $interface  = "{$moduleNs}\\Contracts\\{$module}RepositoryInterface";
                $repository = "{$moduleNs}\\Repository\\{$module}Repository";
                if (interface_exists($interface) && class_exists($repository)) {
                    $bindings[$interface] = $repository;
                }
            }

            foreach (new FilesystemIterator($modulePath, FilesystemIterator::SKIP_DOTS) as $subModuleInfo) {
                if (!$subModuleInfo->isDir()) continue;

                $subModule     = $subModuleInfo->getFilename();
                $subModulePath = $subModuleInfo->getPathname();
                $subModuleNs   = "{$moduleNs}\\{$subModule}";

                if (is_dir("{$subModulePath}/Repository") && is_dir("{$subModulePath}/Contracts")) {
                    $interface  = "{$subModuleNs}\\Contracts\\{$subModule}RepositoryInterface";
                    $repository = "{$subModuleNs}\\Repository\\{$subModule}Repository";
                    if (interface_exists($interface) && class_exists($repository)) {
                        $bindings[$interface] = $repository;
                    }
                }
            }
        }
        return $bindings;
    }
}
