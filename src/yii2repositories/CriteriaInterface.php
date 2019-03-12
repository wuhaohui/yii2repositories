<?php
/**
 * Created by PhpStorm.
 * User: haohui
 * Date: 2019/1/7
 * Time: 16:44
 */

namespace whh\yii2repositories;


interface CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository);
}