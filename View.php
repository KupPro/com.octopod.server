<?php

namespace Octopod\Octophp;

use Octopod\Octophp\Interfaces\Renderable;

class ViewNotFoundException extends \Exception {}

class View implements Renderable {

    protected $app;
    protected $view;
    protected $viewPath;

    protected $cached;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function load($view, $cached = null)
    {
        $this->view = $view = (string) $view;
        if (empty($view)) {
            throw new \InvalidArgumentException();
        }

        $this->viewPath = realpath($this->app['paths']['app'].'/views/'.$view.'.php');
        if (empty($this->viewPath)) {
            throw new ViewNotFoundException($this->view);
        }

        $this->cached = (is_null($cached)) ? Facades\Config::get('default.cache') : $cached;

        return $this;
    }

    public function id()
    {
        return $this->view;
    }

    public function cached()
    {
        return $this->cached;
    }

    public function render()
    {
        if ( ! empty($this->viewPath)) {
            ob_start();
            include $this->viewPath;
            return ob_get_clean();
        } else {
            throw new ViewNotFoundException($this->view);
        }
    }

}