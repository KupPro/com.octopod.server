<?php

namespace Octopod\Octophp;

use Octopod\Octophp\Interfaces\Renderable;

class TemplateNotFoundException extends \Exception {}

class Template implements Renderable {

    protected $app;
    protected $template;
    protected $templatePath;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function load($template)
    {
        $this->template = $template = (string) $template;
        if (empty($template)) {
            throw new \InvalidArgumentException();
        }

        $this->templatePath = realpath($this->app['paths']['app'].'/templates/'.$template.'.php');
        if (empty($this->templatePath)) {
            throw new TemplateNotFoundException($this->template);
        }

        return $this;
    }

    public function id()
    {
        return $this->template;
    }

    public function render()
    {
        if ( ! empty($this->templatePath)) {
            ob_start();
            include $this->templatePath;
            return ob_get_clean();
        } else {
            throw new TemplateNotFoundException($this->template);
        }
    }

}