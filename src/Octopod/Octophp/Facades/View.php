<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class View extends Facade {

    public static function getFacadeAccessor()
    {
        return 'view';
    }

}