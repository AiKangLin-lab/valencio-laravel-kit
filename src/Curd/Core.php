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

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * CRUD操作核心特征
 *
 * 提供完整的CRUD（创建、读取、更新、删除）操作功能
 * 包括列表查询、分页、创建、更新、删除等核心方法
 *
 * @package Valencio\LaravelKit\Curd
 * @author ValencioKang <ailin1219@foxmail.com>
 * @since 2025-08-07
 */
trait Core
{
    use Tools;

    /**
     * 获取数据列表
     *
     * 支持分页查询和普通查询，自动应用搜索、排序、字段选择等条件
     *
     * @return Collection|LengthAwarePaginator 数据列表或分页结果
     */
    public function getList (): Collection|LengthAwarePaginator
    {
        $builder = $this->getCurrentBuilder();

        // 返回数据
        if ($this->isPaginated) {
            $limit = $this->requestParameters[$this->pageSizeField] ?? 10;
            return $this->currentBuilder->paginate($limit);
        }


        return $this->currentBuilder->get();
    }

    /**
     * 获取当前查询构建器实例
     * 创建查询构建器实例并应用搜索、排序、字段选择等条件
     * 返回当前查询构建器实例
     *
     * @param bool $isSetColumns 是否设置列
     * @return Builder
     */
    public function getCurrentBuilder (bool $isSetColumns = true): Builder
    {
        $this->currentBuilder = $this->model::query();
        $this->buildQuery();

        // 设置列
        if ($isSetColumns) {
            $this->setColumns($this->isPaginated ? $this->columns : $this->listColumns);
        }

        $this->setSort();

        $this->setWith();

        // 追加自定义构建逻辑
        $this->handleBuilder();

        $this->isPaginated = isset($this->requestParameters['page']);
        
        return $this->currentBuilder;

    }

    /**
     * 创建新记录
     *
     * 处理请求数据并创建新的模型实例
     *
     * @return Model 新创建的模型实例
     */
    public function store (): Model
    {
        $this->handleData();
        $this->beforeCreate();

        return $this->model::query()->create($this->data);
    }

    /**
     * 更新指定记录
     *
     * 根据ID查找记录并更新其数据
     *
     * @param int $id 要更新的记录ID
     * @return Model 更新后的模型实例
     * @throws ModelNotFoundException 当记录不存在时抛出异常
     */
    public function edit (int $id): Model
    {
        $this->isUpdate = true;
        //  处理数据
        $this->handleData();

        $row = $this->model::query()->findOrFail($id);

        //  修改前置
        $this->beforeUpdate($row);

        if ($this->data) {
            if (!empty($this->allowUpdateFields)) {
                $this->data = array_intersect_key($this->data, array_flip($this->allowUpdateFields));
            }
            $row->update($this->data);
        }
        return $row;
    }

    /**
     * 删除指定记录
     *
     * 根据ID查找并删除记录
     *
     * @param int $id 要删除的记录ID
     * @return bool 删除是否成功
     * @throws ModelNotFoundException 当记录不存在时抛出异常
     */
    public function destroy (int $id): bool
    {
        $row = $this->model::query()->findOrFail($id);
        return $row->delete();
    }

    /**
     * 批量删除记录
     *
     * 根据ID数组批量删除多条记录
     *
     * @param array<int> $ids 要删除的记录ID数组
     * @return int 实际删除的记录数量
     */
    public function batchDestroy (array $ids): int
    {
        return $this->model::destroy($ids);
    }
}
