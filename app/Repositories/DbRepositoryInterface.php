<?php
namespace App\Repositories;

interface DbRepositoryInterface
{
    public function getById($id);

    public function getAll();

    public function save();

    public function edit($id);

}