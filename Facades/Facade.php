<?php

namespace Octopod\Octophp\Facades;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class Facade extends IlluminateFacade {

    protected static $app;

    protected static $resolvedInstance;

    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) return $name;
        return static::$app->make($name);
    }

}