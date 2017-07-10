<?php

namespace App\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Test extends Eloquent {
    protected $connection = 'mongodb';

}
