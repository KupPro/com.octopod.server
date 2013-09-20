<?php

namespace Octopod\Octophp;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class OctophpException extends \Exception {
}

class Application extends Container {

    public function __construct($applicationPath)
    {
        $this['app'] = $this;

        // Check if $applicationPath exists
        $applicationPath = realpath($applicationPath);
        if (empty($applicationPath)) {
            throw new OctophpException("Application Path doesn't exists. Setup your application directory.");
        }

        // Setup application and core paths
        $this['paths'] = array(
            'app' => $applicationPath,
            'octophp' => __DIR__
        );
    }

    public function boot()
    {
        // Setup Facades
        Facade::setFacadeApplication($this);

        // Register bindings into container
        $this->registerProviders();

        // Alias classes into global namespace
        $this->registerAliases();
    }

    /**
     * Register Services
     */
    public function registerProviders()
    {
        $this->instance('Symfony\Component\HttpFoundation\Request', SymfonyRequest::createFromGlobals());
        $this->singleton('Octopod\Octophp\Application', 'app');

        $this->singleton('request', 'Octopod\Octophp\Request');
        $this->singleton('config', function ($app) {
            /** @var Config $config */
            // Create config class instance
            $config = $app->make('Octopod\Octophp\Config');

            // Add application and core repository
            $config->addRepository($app->path('app').'/config/');
            $config->addRepository($app->path('octophp').'/config/');

            return $config;
        });
        $this->singleton('events', 'Illuminate\Events\Dispatcher');
        $this->singleton('handler', 'Octopod\Octophp\Handler');
        $this->singleton('response', 'Octopod\Octophp\Response');
        $this->singleton('uri', 'Octopod\Octophp\Uri');

        $this->bind('template', 'Octopod\Octophp\Template');
        $this->bind('view', 'Octopod\Octophp\View');

        // temp
        Octopod::init($this['request']);
    }

    public function registerAliases()
    {
        AliasLoader::getInstance(array(
            'App' => 'Octopod\Octophp\Facades\App',
            'Handler' => 'Octopod\Octophp\Facades\Handler',
            'Event' => 'Octopod\Octophp\Facades\Event',
            'Request' => 'Octopod\Octophp\Facades\Request',
            'Response' => 'Octopod\Octophp\Facades\Response',
            'Config' => 'Octopod\Octophp\Facades\Config',
            'Template' => 'Octopod\Octophp\Facades\Template',
            'View' => 'Octopod\Octophp\Facades\View',

            'Octopod' => 'Octopod\Octophp\Octopod',
            // temp
        ))->register();
    }

    public function run()
    {
        $handlerId = $this['request']->getHandler();

        try {
            $this['handler']->run($handlerId);
        } catch (HandlerNotFoundException $e) {
            try {
                $view = $this['view']->load($handlerId);
                $this['response']->addView($view);
            } catch (ViewNotFoundException $e) {

                // nothing found (no handler, no view)
                // what to do then? @todo:
                throw new \Exception('handler not found');

            }
        }

        $this['response']->send();
    }

    public function path($path = 'app')
    {
        $paths = $this->make('paths');
        if (array_key_exists($path, $paths)) {
            return $paths[$path];
        }
        else {
            throw new OctophpException("Cannot find path for key '$path'.");
        }
    }

}