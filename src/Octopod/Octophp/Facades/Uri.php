<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class Uri extends Facade {

    public static function getFacadeAccessor()
    {
        return 'uri';
    }

}