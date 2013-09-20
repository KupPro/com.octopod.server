<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class Handler extends Facade {

    public static function getFacadeAccessor()
    {
        return 'handler';
    }

}