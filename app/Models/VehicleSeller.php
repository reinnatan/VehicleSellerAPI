<?php
namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class VehicleSeller extends Eloquent{
    protected $connection = 'mongodb';
    protected $collection = 'vehicleseller';
} 
