<?php

namespace whh\yii2repositories;

use yii\base\InvalidArgumentException;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

abstract class BaseRepository implements RepositoryInterface, RepositoryCriteriaInterface
{
    protected $app;

    protected $criteria;
    protected $skipCriteria;

    /**
     * @var \Closure
     */
    protected $scopeQuery = null;

    /**
     * @var ActiveRecord
     */
    protected $model;

    /**
     * @var ActiveQuery
     */
    protected $query;

    public function __construct($app = null)
    {
        $this->app = $app;
        $this->criteria = array();
        $this->makeModel();
        $this->makePresenter();
        $this->boot();
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    abstract public function model();


    public function boot()
    {

    }

    public function makeModel()
    {
        $model = Container::make($this->model());

        if (!$model instanceof ActiveRecord) {
            throw new InvalidArgumentException("Class {$this->model()} must be an instance of yii\\base\\model");
        }

        $this->query = $model::find();
        return $this->model = $model;
    }


    public function makePresenter()
    {

    }


    public function resetModel()
    {
        $this->makeModel();
    }


    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new InvalidArgumentException("Class " . get_class($criteria) . " must be an instance of common\\CriteriaInterface");
        }
        $this->criteria[] = $criteria;
        return $this;
    }

    public function popCriteria($criteria)
    {
        $this->criteria = ArrayHelper::removeValue($this->criteria, $criteria);

        return $this;
    }

    public function getCriteria()
    {
        return $this->criteria;
    }

    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->query = $criteria->apply($this->query, $this);
        $results = $this->query->all();
        $this->resetModel();

        return $results;
    }

    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * @return $this|RepositoryCriteriaInterface
     */
    public function resetCriteria()
    {
        $this->criteria = [];
        return $this;
    }


    protected function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaInterface) {
                    $this->query = $c->apply($this->query, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Apply scope in current Query
     *
     * @return $this
     */
    protected function applyScope()
    {
        if (isset($this->scopeQuery) && is_callable($this->scopeQuery)) {
            $callback = $this->scopeQuery;
            $this->query = $callback($this->query);
        }
        return $this;
    }


    public function scopeQuery(\Closure $scope)
    {
        $this->scopeQuery = $scope;

        return $this;
    }

    public function resetScope()
    {
        $this->scopeQuery = null;

        return $this;
    }

    /**
     * 查询不到就抛出404页面
     * @param $id
     * @param array $columns
     * @return ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $model = $this->findOne($id, $columns = ['*']);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException();
        }
        return $model;
    }

    /**
     * @param $id
     * @return ActiveRecord
     */
    public function findOrCreate($id)
    {
        if (empty($id)){
            return new $this->model();
        }

        $model = $this->findOne($id);
        if ($model === null) {
            $model = new $this->model();
        }

        return $model;
    }

    public function findOne($id, $columns = ['*'])
    {
        if (isset($this->model->entityPrimaryKey)){
            $primaryKey = $this->model->entityPrimaryKey;
        }else{
            $primaryKey = $this->model::primaryKey();
        }

        $this->applyCriteria();
        $this->applyScope();
        $this->query->select($columns);

        if (is_array($id)) {
            $model = $this->query->andWhere($id)->one();
        } else {
            $model = $this->query->andWhere([$primaryKey => $id])->one();;
        }

        $this->resetModel();

        return $model;
    }

    /**
     * @param $filed
     * @param $value
     * @param string $columns
     * @return ActiveQuery
     */
    public function findByField($filed, $value, $columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->query->andWhere([$filed => $value])->select($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * @param string $columns
     * @return ActiveQuery
     */
    public function findAll($columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->query->select($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * @param string $columns
     * @return ActiveRecord[]
     */
    public function all($columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->query->select($columns);
        $this->resetModel();

        return $model->all();
    }

    /**
     * @param array $where
     * @param string $columns
     * @return ActiveQuery
     */
    public function findWhere(array $where, $columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $this->applyConditions($where);
        $model = $this->query->select($columns);
        $this->resetModel();

        return $model;
    }

    /**
     * @param $field
     * @param array $where
     * @param string $columns
     * @return array|ActiveRecord[]
     */
    public function findWhereIn($field, array $where, $columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->query->andWhere(['in', $field, $where])->select($columns)->all();
        $this->resetModel();

        return $model;
    }

    /**
     * @param $field
     * @param array $where
     * @param string $columns
     * @return array|ActiveRecord[]
     */
    public function findWhereNotIn($field, array $where, $columns = "*")
    {
        $this->applyCriteria();
        $this->applyScope();
        $model = $this->query->andWhere(['not in', $field, $where])->select($columns)->all();
        $this->resetModel();

        return $model;
    }

    public function firstOrCreate(array $attributes = [])
    {

    }

    public function joinWith($with, $eagerLoading = true, $joinType = 'LEFT JOIN')
    {
        $this->query->joinWith($with, $eagerLoading, $joinType);
        return $this;
    }


    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($condition, $field, $val) = $value;
                $this->model = $this->query->andWhere([$field, $condition, $val]);
            } else {
                $this->model = $this->query->andWhere(['=', $field, $value]);
            }
        }
    }

    public function paginate($limit = null, $columns = ['*'])
    {
        $this->applyCriteria();
        $this->applyScope();

        $count = $this->query->count();

        $pagination = new Pagination([
            'totalCount' => $count,
            'defaultPageSize' => $limit
        ]);

        $results = $this->query->select($columns)
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $this->resetModel();

        $object = new \stdClass();
        $object->data = $results;
        $object->pagination = $pagination;

        return $object;
    }

    public function create(array $attributes)
    {
        $model = new $this->model;
        $model->setAttributes($attributes);
        return $model->save();
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->query = $this->query->with($relations);

        return $this;
    }
}
