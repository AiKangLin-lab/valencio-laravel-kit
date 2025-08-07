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
 * 核心功能
 */
trait Tools
{
    use Properties;

    /**
     * @return void
     */
    public function buildQuery (): void
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
     * 设置排序
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
        if (!empty($params['order']) && in_array($params['order'], ['asc', 'desc'])) {
            $this->sortOrder = $this->requestParameters['order'];
        }

        $this->currentBuilder->orderBy($this->sortField, $this->sortOrder);
    }


    /**
     * 处理数据
     *
     * Author        : Collin Ai
     * Achieve success and win recognition
     */
    protected function handleData (): void
    {
        $this->data = $this->requestParameters;
    }

    /**
     * 新增前置
     *
     * @return void
     */
    protected function beforeCreate (): void
    {
    }


    /**
     * 修改前置
     *
     * @param $row
     * @return void
     */
    public function beforeUpdate ($row): void
    {
    }
}
