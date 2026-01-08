<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  StorageAdapterRegistry.php
// +----------------------------------------------------------------------
// | Year:      2026/1/8/一月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\File\Adapters;


use RuntimeException;

/**
 * 存储适配器注册表
 *
 * 负责：
 * - 按 disk 获取对应的 adapter
 * - 避免在业务代码中写 switch/match
 */
class StorageAdapterRegistry
{
    /**
     * @var array<string, StorageAdapterInterface>
     */
    private array $adapters = [];

    /**
     * @param iterable<StorageAdapterInterface> $adapters
     */
    public function __construct (iterable $adapters)
    {
        foreach ($adapters as $adapter) {
            $this->adapters[$adapter->disk()] = $adapter;
        }
    }


    /**
     * 根据 disk 获取适配器
     *
     * @param string $disk
     * @return StorageAdapterInterface
     */
    public function get (string $disk): StorageAdapterInterface
    {
        if (!isset($this->adapters[$disk])) {
            throw new RuntimeException("No adapter found for disk: $disk");
        }

        return $this->adapters[$disk];
    }
}
