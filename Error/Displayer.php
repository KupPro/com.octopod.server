<?php

namespace Octopod\Octophp\Error;


use Exception;

class Displayer {

    public function display(Exception $e)
    {
        echo $this->formatException($e);
    }

    protected function formatException(Exception $e)
    {
        $location = $e->getMessage().' in '.$e->getFile().':'.$e->getLine();
        return 'Error: '.$location;
    }

}