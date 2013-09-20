<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class Request extends Facade {

    public static function getFacadeAccessor()
    {
        return 'request';
    }

}