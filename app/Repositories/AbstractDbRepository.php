<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractDbRepository implements DbRepositoryInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find($id, $columns = array('*'))
    {
        return call_user_func_array("{$this->getModelClassName($this->model)}::find", array($id, $columns));
    }

    public function all($columns = array('*'))
    {
        return call_user_func_array("{$this->getModelClassName($this->model)}::all", array($columns));
    }

    public function create(array $attributes)
    {
        return call_user_func_array("{$this->getModelClassName($this->model)}::create", array($attributes));
    }

    public function update(array $data, $column, $value, $operator = '=') {
        $where = call_user_func_array("{$this->getModelClassName($this->model)}::where", array($column, $operator, $value));
        return $where->update($data);
    }
    
    public function destroy($ids)
    {
        return call_user_func_array("{$this->getModelClassName($this->model)}::destroy", array($ids));
    }

    protected function getModelClassName(Model $model)
    {
        return get_class($model);
    }
}