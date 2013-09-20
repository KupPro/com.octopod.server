<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class Template extends Facade {

    public static function getFacadeAccessor()
    {
        return 'template';
    }

}