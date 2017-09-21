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

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function edit($id)
    {
        // TODO: Implement edit() method.
    }
}