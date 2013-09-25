<?php

namespace Octopod\Octophp\Facades;


use Octopod\Octophp\OctophpException;

class App extends Facade {

    public static function getFacadeAccessor()
    {
        return 'app';
    }

}