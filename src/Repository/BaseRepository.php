<?php
// +----------------------------------------------------------------------
// | Success, real success,
// | is being willing to do the things that other people are not.
// +----------------------------------------------------------------------
// | Author:    ValencioKang <ailin1219@foxmail.com>
// +----------------------------------------------------------------------
// | FileName:  BaseRepository.php
// +----------------------------------------------------------------------
// | Year:      2025/8/14/八月
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace Valencio\LaravelKit\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{

    /**
     * @var Model
     */
    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct (Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get the base query builder instance.
     *
     * @return Builder
     */
    abstract protected function baseQuery (): Builder;


    /**
     * @param int $id
     * @param array $columns
     * @return Model|null
     */
    public function find (int $id, array $columns = ['*']): ?Model
    {
        return $this->baseQuery()->select($columns)->find($id);
    }

    /**
     * @param int $id
     * @param array $columns
     * @return Model
     */
    public function findOrFail (int $id, array $columns = ['*']): Model
    {
        return $this->baseQuery()->select($columns)->findOrFail($id);
    }

    /**
     * @param array $criteria
     * @param array $columns
     * @return Model|null
     */
    public function first (array $criteria, array $columns = ['*']): ?Model
    {
        $query = $this->baseQuery();
        foreach ($criteria as $field => $value) {
            $query->where($field, $value);
        }
        return $query->select($columns)->first();
    }

    /**
     * @param array $criteria
     * @param array $columns
     * @return Model
     */
    public function firstOrFail (array $criteria, array $columns = ['*']): Model
    {
        $query = $this->baseQuery();
        $query->where($criteria);

        return $query->select($columns)->firstOrFail();
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create (array $data): Model
    {
        return $this->baseQuery()->create($data);
    }

    /**
     * @param array $values
     * @return bool
     */
    public function insert (array $values): bool
    {
        return $this->baseQuery()->insert($values);
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update (int $id, array $data): bool
    {
        $model = $this->findOrFail($id);
        return $model->update($data);
    }

    /**
     * @param Model $model
     * @param array $data
     * @return bool
     */
    public function updateModel (Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function delete (Model $model): bool
    {
        return $model->delete();
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function deleteByWhere (array $where): mixed
    {
        return $this->baseQuery()->where($where)->delete();
    }

    /**
     * @param int|array $ids
     * @return int
     */
    public function destroy (int|array $ids): int
    {
        return $this->model::destroy($ids);
    }

    /**
     * @param array $columns
     * @param array $query
     * @return Collection
     */
    public function getAll (array $columns = ['*'], array $query = []): Collection
    {
        return $this->baseQuery()->where($query)->select($columns)->get();
    }
}
