<?php
namespace App\Repositories;

interface DbRepositoryInterface
{
    public function find($id, $columns);

    public function all($columns);

    public function create(array $attributes);

    public function update(array $data, $column, $value, $operator);

    public function destroy($ids);
}