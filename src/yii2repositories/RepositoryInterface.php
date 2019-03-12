<?php
/**
 * Created by PhpStorm.
 * User: haohui
 * Date: 2019/1/7
 * Time: 16:45
 */

namespace wuhaohui\yii2repositories;


interface RepositoryInterface
{
    public function firstOrCreate(array $attributes = []);

    public function findByField($filed, $value, $columns = "*");

    public function findOrFail($id);

    public function findWhere(array $where, $columns = "*");

    public function findWhereIn($field, array $where, $columns = "*");

    public function findWhereNotIn($field, array $where, $columns = "*");


    /**
     * Query Scope
     *
     * @param \Closure $scope
     *
     * @return $this
     */
    public function scopeQuery(\Closure $scope);

    /**
     * Reset Query Scope
     *
     * @return $this
     */
    public function resetScope();
}