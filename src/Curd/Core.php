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

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * 核心功能
 */
trait Core
{
    use Tools;


    public function getList ()
    {
        $this->currentBuilder = $this->model::query();
        $this->buildQuery();

        $this->setSort();

        $this->setColumns();

        $this->setWith();

        $this->handleBuilder();

        if (isset($this->requestParameters['page'])) {
            $list = $this->currentBuilder->paginate($this->requestParameters['limit'] ?? 10);
        } else {
            $list = $this->currentBuilder->get();
        }

        return $list;
    }


    /**
     * 新增
     *
     * @return Model
     */
    public function store (): Model
    {
        $this->handleData();
        $this->beforeCreate();

        return $this->model::query()->create($this->data);
    }

    /**
     * 修改
     *
     * @param int $id
     * @return Model
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
            $row->update($this->data);
        }
        return $row;
    }


    /**
     * 删除
     *
     * @param int $id
     * @return bool
     */
    public function destroy (int $id): bool
    {
      $row = $this->model::query()->findOrFail($id);
      return $row->delete();
    }


    /**
     * 批量删除
     *
     * @param array $ids
     * @return int
     */
    public function batchDestroy (array $ids): int
    {
      return $this->model::destroy($ids);
    }
}
