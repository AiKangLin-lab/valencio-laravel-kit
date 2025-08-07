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

/**
 *
 */

namespace Valencio\LaravelKit\Curd;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * 核心功能
 */
trait Properties
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * 当前builder 实体
     * @var Builder
     */
    protected Builder $currentBuilder;

    /**
     * 排序字段
     *
     * @var string
     */
    protected string $sortField = 'id';
    protected string $sortOrder = 'desc';

    /**
     * 系统自定义排序字段
     *
     * @var array
     */
    protected array $systemSortFields = [];

    /**
     * 是否更新
     *
     * @var bool
     */
    protected bool $isUpdate = false;


    /**
     * 操作数据
     *
     * @var array
     */
    protected array $data = [];


    /**
     * 定义此字段 支持模糊查询
     *
     * @var array
     */
    protected array $searchLikeFields = [];

    /**
     * 搜索字段 此字段精确查询
     *
     * @var array
     */
    protected array $searchEqualFields = [];

    /**
     * 关联模型
     *
     * @var array
     */
    protected array $with = [];

    /**
     * 列表字段 || 查询字段
     *
     * @var array
     */
    protected array $columns = [];
}
