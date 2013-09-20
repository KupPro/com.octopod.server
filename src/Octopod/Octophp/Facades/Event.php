<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade;


class Event extends Facade {

    public static function getFacadeAccessor()
    {
        return 'events';
    }

}