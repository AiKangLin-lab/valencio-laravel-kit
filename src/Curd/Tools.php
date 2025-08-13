<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  Core.php
// +----------------------------------------------------------------------
// | Year:      2025/8/7/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Curd;

use Carbon\Carbon;

/**
 * CRUD操作工具特征
 *
 * 提供CRUD操作的工具方法，包括查询构建、排序、字段设置、数据处理等
 *
 * @package Valencio\LaravelKit\Curd
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-07
 */
trait Tools
{
    use Properties;

    /**
     * 构建查询条件
     *
     * 根据请求参数构建WHERE条件，支持模糊查询、精确查询和时间范围查询
     *
     * @return void
     */
    protected function buildQuery (): void
    {
        $where = [];
        foreach ($this->requestParameters as $field => $value) {
            if (!empty($value)) {
                if (in_array($field, $this->searchLikeFields)) {
                    $where[] = [$field, 'like', "%{$value}%"];
                }
                if (in_array($field, $this->searchEqualFields)) {
                    $where[] = [$field, '=', $value];
                }
            }
            //  特殊字段
            if ($field === 'created_at_start') {
                $where[] = ['created_at', '>=', strtotime($value)];
            }
            if ($field === 'created_at_end') {
                $where[] = ['created_at', '<=', Carbon::parse($value)->endOfDay()->getTimestamp()];
            }
        }

        $this->currentBuilder->where($where);
    }

    /**
     * 设置排序规则
     *
     * 根据请求参数和系统配置设置查询结果的排序方式
     *
     * @return void
     */
    protected function setSort (): void
    {
        // 设置的系统自定义排序
        foreach ($this->systemSortFields as $field => $direction) {
            if ($this->sortField !== $field) {
                $this->currentBuilder->orderBy($field, $direction);
            }
        }

        if (!empty($this->requestParameters['sort']) && is_string($this->requestParameters['sort'])) {
            $this->sortField = $this->requestParameters['sort'];
        }
        if (!empty($this->requestParameters['order']) && in_array($this->requestParameters['order'], ['asc', 'desc'])) {
            $this->sortOrder = $this->requestParameters['order'];
        }

        $this->currentBuilder->orderBy($this->sortField, $this->sortOrder);
    }

    /**
     * 设置查询字段
     *
     * 根据传入的字段列表设置SELECT查询的字段
     *
     * @param array $columns 查询字段列表
     * @return void
     */
    protected function setColumns (array $columns = []): void
    {
        if (!empty($columns)) {
            $this->currentBuilder->select($columns);
        } else {
            $this->currentBuilder->select(['*']);
        }
    }

    /**
     * 设置关联预加载
     *
     * 根据配置的关联模型列表设置WITH预加载
     *
     * @return void
     */
    protected function setWith (): void
    {
        if (!empty($this->with)) {
            $this->currentBuilder->with($this->with);
        }
    }

    /**
     * 处理查询构建器
     *
     * 子类可以重写此方法来自定义查询构建器的处理逻辑
     *
     * @return void
     */
    protected function handleBuilder (): void
    {
    }

    /**
     * 处理请求数据
     *
     * 将请求参数转换为待操作的数据数组
     * 子类可以重写此方法来自定义数据处理逻辑
     *
     * Author        : Collin Ai
     * Achieve success and win recognition
     *
     * @return void
     */
    protected function handleData (): void
    {
        $this->data = $this->requestParameters;
    }

    /**
     * 创建记录前的处理
     *
     * 在创建新记录之前执行的自定义逻辑
     * 子类可以重写此方法来实现创建前的数据处理、验证等
     *
     * @return void
     */
    protected function beforeCreate (): void
    {
    }

    /**
     * 更新记录前的处理
     *
     * 在更新记录之前执行的自定义逻辑
     * 子类可以重写此方法来实现更新前的数据处理、验证等
     *
     * @param Model $row 要更新的模型实例
     * @return void
     */
    protected function beforeUpdate ($row): void
    {
    }
}
