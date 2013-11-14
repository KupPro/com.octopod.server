<?php

namespace Octopod\Octophp;

class HandlerNotFoundException extends \Exception {}

class Handler {

    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string $handlerId
     * @throws \Octopod\Octophp\HandlerNotFoundException
     */
    public function run($handlerId)
    {
        $handlerId = (string) $handlerId;
        if (empty($handlerId)) {
            return;
        }

        $handlerPath = realpath($this->app['paths']['app'].'/handlers/'.$handlerId.'.php');
        if (empty($handlerPath)) {
            throw new HandlerNotFoundException();
        }
        $this->app['response']->setType("serverRequest");

        include $handlerPath;
    }

}