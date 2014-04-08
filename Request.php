<?php


namespace Octopod\Octophp;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\ParameterBag;

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', str_replace('_', ' ', substr($name, 5)))] = $value;
            }
        }
        return $headers;
    }
}


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

    public function __construct(SymfonyRequest $symfonyRequest, ParameterBag $parameters) {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists('octopodprotocolversion', $headers) and $headers['octopodprotocolversion'] == 2) {
            $params = new ParameterBag(json_decode($_POST['mainParams'], true));
        } else $params = $parameters;
        $this->symfonyRequest = $symfonyRequest;
        $this->parameters = $params;

        $this->prepareRequest();
    }


    protected function prepareRequest() {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists('octopodprotocolversion', $headers) and $headers['octopodprotocolversion'] == 2) {
//
        } else {
            $this->data = $this->symfonyRequest->getContent();
            $data = json_decode($this->data, true);

            // @todo: check for json errors


            if (is_array($data)) {
                $this->parameters->add($data);
            } else {
                // @todo: log error
            }
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
        if (!is_null($key)) {
            return urldecode($this->parameters->get("parameters[$key]", $default, true));
        } else {
            return $this->parameters->get("parameters");
        }
    }

    public function paramArray($key)
    {
        if (!is_null($key)) {

            foreach (($this->parameters->get("parameters")) as $parameterKey => $value) {
                $splitResult = explode("_", $parameterKey);
                if (sizeof($splitResult)>1)
                {
                    list ($pKey, $pValue) = $splitResult;
                    if ($pKey == $key)
                        $result[$pValue] = urldecode($value);
                }
            }
            if (sizeof($result))
                return $result;
        }

        return false;

    }

    public function attach($key = null, $default = null) {
        $headers = getallheaders();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists('octopodprotocolversion', $headers) and $headers['octopodprotocolversion'] == 2) {
            if (!is_null($key)) {
                return $_FILES[$key];
            } else {
                return $_FILES;
            }
        } else {
            if (!is_null($key)) {
                return $this->parameters->get("files[$key]", $default, true);
            } else {
                return $this->parameters->get("files");
            }
        }
    }

    public function saveAttachToFile($key, $path) {
        if ( ! is_null($key) && ! is_null($path)) {
            return file_put_contents($path, $this->parameters->get("files[$key]", null, true));
        } else {
            return false;
        }
    }

    public function getSymfonyRequest()
    {
        return $this->symfonyRequest;
    }

}