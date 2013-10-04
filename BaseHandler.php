<?php

namespace Octopod\Octophp;

/**
 * Class Handler - base class that all application handlers should extend
 *
 * @package Octopod\Octophp
 */
class BaseHandler {

    protected $app;
    protected $request;
    protected $response;

    public function __construct(Application $app, Request $request, Response $response)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = $response;
    }

    public function handle()
    {
        echo 'Handler is not found.';
    }

    /**
     * Execute another handler
     *
     * @param string $handler
     */
    protected function executeHandler($handler)
    {

    }

    protected function addView()
    {

    }

    protected function passParam()
    {

    }

    protected function takeParam($key)
    {
        return $this->request->info($key);
    }

}