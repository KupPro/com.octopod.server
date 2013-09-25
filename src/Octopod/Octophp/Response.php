<?php

namespace Octopod\Octophp;

use Octopod\Octophp\Interfaces\Renderable;

/**
 * Class Response — generates and sends full response XML
 *
 * @package Octopod\Octophp
 */
class Response implements Renderable {

    const TEMPLATE_FILE = 'responseTemplate.php';

    protected $views;
    protected $responseParameters;

    protected $sessionId;
    protected $debug;
    protected $orientation;
    protected $cacheImagesCounter;
    protected $cacheMarkupCounter;
    protected $installationId;

    protected $settings;
    protected $variables;
    protected $badgeCounter;

    protected $actions;
    protected $scripts;
    protected $systemEvents;

    protected $resources;
    protected $queries;

    public function __construct()
    {
        $this->sessionId = '';
        $this->debug = Facades\Config::get('debug');
        $this->orientation = Facades\Config::get('default.orientation');
        $this->cacheImagesCounter = 0;
        $this->cacheMarkupCounter = 0;
        $this->installationId = '';
    }

    public function setParameter($key, $value)
    {
        $this->responseParameters[$key] = $value;
    }

    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param Renderable $scripts
     * @throws \InvalidArgumentException
     */
    public function setScripts($scripts)
    {
        if ( ! $scripts instanceof Renderable) {
            throw new \InvalidArgumentException("Response scripts should implement Renderable interface.");
        }
        $this->scripts = $scripts;
    }

    /**
     * @param Renderable $view
     * @throws \InvalidArgumentException
     */
    public function addView($view)
    {
        if ( ! $view instanceof Renderable) {
            throw new \InvalidArgumentException("View should implement Renderable interface.");
        }
        $this->views[] = $view;
    }

    public function addResource($filename, $url)
    {
        $this->resources[] = array(
            'filename' => $filename,
            'url' => $url
        );
    }

    /**
     * @param string $query
     */
    public function addQuery($query)
    {
        $this->queries .= $query;
    }

    public function render() {
        ob_start();

        $filePath = realpath(\App::path('octophp').'/responseTemplate.php');
        if (empty($filePath)) {
            throw new OctophpException("Response template cannot be found at $filePath. Check your installation.");
        }
        include $filePath;

        return ob_get_clean();
    }

    public function send()
    {
        echo $this->render();
    }

    /* TEMPORARY */
    public function get($key, $default = null)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        } else {
            return $default;
        }
    }

}