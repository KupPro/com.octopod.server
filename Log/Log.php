<?php

namespace Octopod\Octophp\Log;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log {

    protected $log;
    protected $request;

    protected $levels = array(
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    );

    public function __construct()
    {
        $this->log = new Logger('log');
        $this->log->pushHandler(new RotatingFileHandler(\App::path('storage').'/log/log.txt', 0));

        $this->request = new Logger('request');
        $handler = new RotatingFileHandler(\App::path('storage').'/log/request.txt', 0);
        $handler->setFormatter(new JsonFormatter());
        $this->request->pushHandler($handler);
    }

    public function request()
    {
        $info = \Request::info();
        $this->request->addInfo('request', $info);
    }

    public function __call($method, $parameters)
    {
        if (in_array($method, $this->levels))
        {

            $method = 'add'.ucfirst($method);
            return call_user_func_array(array($this->log, $method), $parameters);
        }

        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

}