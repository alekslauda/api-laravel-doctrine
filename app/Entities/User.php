<?php

namespace App\Entities;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class User
 * @package App\Entities
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements \Illuminate\Contracts\Auth\Authenticatable
{
    use \LaravelDoctrine\ORM\Auth\Authenticatable;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getPassword()
    {
        return $this->password;
    }

}