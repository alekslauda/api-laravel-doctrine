<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Tymon\JWTAuth\JWTAuth;
class UserController extends Controller
{
    public function single(JWTAuth $JWTAuth)
    {
        echo 'some protected data';
        echo '<hr>';
        dd($JWTAuth->parseToken()->toUser()->name);
    }

    public function all()
    {

    }

}
