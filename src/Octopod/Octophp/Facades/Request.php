<?php

namespace Octopod\Octophp\Facades;




class Request extends Facade {

    public static function getFacadeAccessor()
    {
        return 'request';
    }

}