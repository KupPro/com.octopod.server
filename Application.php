<?php

namespace Octopod\Octophp;

use Illuminate\Container\Container;
use Octopod\Octophp\Facades\Facade;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class OctophpException extends \Exception {
}

class Application extends Container {

    protected $booted = false;

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
        if ($this->booted)
            return;

        // Setup Facades
        Facade::setFacadeApplication($this);
        // Register bindings into container
        $this->registerProviders();
        // Alias classes into global namespace
        $this->registerAliases();

        $this->booted = true;
    }

    /**
     * Register Services
     */
    public function registerProviders()
    {
        $this->singleton('Symfony\Component\HttpFoundation\Request', function () {
            return SymfonyRequest::createFromGlobals();
        });
        $this->singleton('symfony.request', 'Symfony\Component\HttpFoundation\Request');
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
        $this->singleton('log', 'Octopod\Octophp\Log\Log');

        $this->bind('template', 'Octopod\Octophp\Template');
        $this->bind('view', 'Octopod\Octophp\View');
    }

    public function registerAliases()
    {
        AliasLoader::getInstance($this['config']->get('aliases'))->register();
    }

    public function platform()
    {
        // temp
        Octopod::init($this['request']);
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

    public function run()
    {
        $this->boot();

        // Get request path
        $path = $this['symfony.request']->getPathInfo();
        $path = trim($path, '/');
        if (empty($path)) $path = '/';

        // Paths and their controllers
        $routes = array(
            '/' => array($this, 'platform'),
            'init' => array($this, 'init'),
            'init/scriptoffset' => array($this, 'init_scriptoffset'),
        );

        if (isset($routes[$path]) AND is_callable($routes[$path])) {
            call_user_func_array($routes[$path], array());
        }
    }

    public function init()
    {
        include $this->path('octophp').'/init/view.php';
    }

    public function init_scriptoffset()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            return;
        }

        $action = $_POST['action'];
        $url = $_POST['url'];
        $offset = $_POST['offset'];

        $step = 1;

        if (empty($action)) return;
        if (empty($url)) return;

        if ($offset == 0) {
            include $this->path('octophp').'/init/init.php';
        }

        $generatedPath = Facades\App::path('generated').'/';
        $resourcesPath = Facades\App::path('resources').'/';

        include Facades\App::path('octophp').'/init/serveImage.php';

        $imagesInitList = include $generatedPath . 'data/imagesInitList.php';

        $count = count($imagesInitList);

        serveImage($imagesInitList[$offset]);

        $offset = $offset + $step;
        if ($offset >= $count) {
            saveImageListToArray();
            $success = 1;
        } else {
            $success = round($offset / $count, 4);
        }

        $output = array('offset' => $offset, 'success' => $success);
        echo json_encode($output);
    }

    public function path($path = 'app')
    {
        $paths = $this->make('paths');
        if (array_key_exists($path, $paths)) {
            return $paths[$path];
        }
        if ($cPath = Facades\Config::get("paths.$path")) {
            return $this->path().$cPath;
        }
        throw new OctophpException("Cannot find path for key '$path'.");
    }

}