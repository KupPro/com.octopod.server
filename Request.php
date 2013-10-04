<?php


namespace Octopod\Octophp;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class Request
 *
 * @package Octopod\Octophp
 */
class Request {

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $symfonyRequest;

    /**
     * Request data
     *
     * @var array
     */
    protected $data = array();

    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    public function __construct(SymfonyRequest $symfonyRequest, ParameterBag $parameters)
    {
        $this->symfonyRequest = $symfonyRequest;
        $this->parameters = $parameters;

        $this->prepareRequest();
    }

    protected function prepareRequest()
    {
        $this->data = $this->symfonyRequest->getContent();
        $data = json_decode($this->data, true);

        // @todo: check for json errors


        if (is_array($data)) {
            $this->parameters->add($data);
        }
        else {
            // @todo: log error
        }
    }

    public function getHandler()
    {
        if (is_null($handler = $this->symfonyRequest->get('handlerId'))) {
            $handler = $this->info('handlerId');
        }
        if (empty($handler)) {
            $handler = Facades\Config::get('default.handler');
        }

        return $handler;
    }

    public function info($key = null, $default = null)
    {
        if ( ! is_null($key)) {
            return $this->parameters->get($key, $default);
        } else {
            return $this->parameters->all();
        }
    }

    public function param($key = null, $default = null) {
        if ( ! is_null($key)) {
            return $this->parameters->get("parameters[$key]", $default, true);
        } else {
            return $this->parameters->get("parameters");
        }
    }

    public function getSymfonyRequest()
    {
        return $this->symfonyRequest;
    }

}