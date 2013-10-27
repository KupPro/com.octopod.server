<?php

namespace Octopod\Octophp\Error;


use Exception;

class Displayer {

    public function display(Exception $e)
    {
        echo '<pre>';
        echo $this->formatException($e);
    }

    public function formatException(Exception $e)
    {
        $message = get_class($e).': '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine();
        return $message;
    }

}