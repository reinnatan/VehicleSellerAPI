<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserJWT extends Eloquent
{
    protected $connection='mongodb';
    protected $collection='userjwt';
}