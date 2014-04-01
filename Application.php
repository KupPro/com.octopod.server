<?php

namespace Octopod\Octophp;

use Illuminate\Container\Container;
use Octopod\Octophp\Facades\Facade;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

define("OCTOLOG_NONE", 0);
define("OCTOLOG_TEXT", 1);
define("OCTOLOG_ALERT", 2);
define("OCTOLOG_MAIL", 4);
define("OCTOLOG_ALL", 7);

class OctophpException extends \Exception
{
}

class Application extends Container
{

    protected $booted = false;

    public function __construct($rootDir, $applicationPath , $dirPath = null, $clientAppPath = null)
    {
        $this['app'] = $this;

        // Check if $applicationPath exists

        if($clientAppPath != null) {
            $applicationPathCheck = realpath($dirPath . $clientAppPath);
        } else {
            $applicationPathCheck = realpath($rootDir . '/' . $applicationPath);
        }

        if (empty($applicationPathCheck)) {
            throw new OctophpException("Application Path doesn't exists. Setup your application directory.");
        }

        // Setup application and core paths
        $this['paths'] = array(
            'app' => $rootDir . '/' . $applicationPath,
			'relativeAppPath' => $applicationPath,
            'dir' => $dirPath,
            'clientAppPath' => $clientAppPath,
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

        // Set timezone
        date_default_timezone_set(\Config::get('timezone'));

        $this->booted = true;
    }

    /**
     * Register Services
     */
    public function registerProviders()
    {
        // Error Handler
        $this->instance('error', $this->make('Octopod\Octophp\Error\Handler')->register());

        $this->singleton('log', 'Octopod\Octophp\Log\Log');
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
            if ($app->path('clientAppPath') == null) {
                $config->addRepository($app->path('dir') . $app->path('app') . '/config/'); // todo: ololo
            } else {
                $config->addRepository($app->path('dir') . $app->path('clientAppPath') . '/config/');
            }
            $config->addRepository($app->path('octophp') . '/config/');

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
        AliasLoader::getInstance($this['config']->get('aliases'))->register();
    }

    public function platform()
    {
        $handlerId = $this['request']->getHandler();
        try {
            $this['handler']->run($handlerId);
        } catch (HandlerNotFoundException $e) {
            try {
                $view = $this['view']->load($handlerId);
                $this['response']->addView($view);
                $this['response']->setType("viewRequest");
            } catch (ViewNotFoundException $e) {
                \Log::error("system", "Handler or view not found for handlerId=" . $handlerId);
                die();
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
            'log' => array($this, 'logViewer'),
            'logFile' => array($this, 'logFileViewer'),
        );

        if (isset($routes[$path]) AND is_callable($routes[$path])) {
            call_user_func_array($routes[$path], array());
        }
    }

    public function init()
    {
        include $this->path('octophp') . '/init/view.php';
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
            include $this->path('octophp') . '/init/init.php';
        }

//        $generatedPath = Facades\App::path('generated') . '/'; // todo: ololo
//        $resourcesPath = Facades\App::path('resources') . '/'; // todo: ololo

        if ($this->path('clientAppPath') != null) {
            $generatedPath = $this->path('dir') . $this->path('clientAppPath') . Config::get('paths.generated') . '/';
            $resourcesPath = $this->path('dir') . $this->path('clientAppPath') . Config::get('paths.resources') . '/';
        } else {
            $generatedPath = $this->path('app') . Config::get('paths.generated') . '/';
            $resourcesPath = $this->path('app') . Config::get('paths.resources') . '/';
        }

        include Facades\App::path('octophp') . '/init/serveImage.php';

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

    public function logViewer()
    {
        include $this->path('octophp') . '/LogViewer/index.php';
    }

    public function logFileViewer()
    {
        include $this->path('octophp') . '/LogViewer/file.php';
    }


    public function path($path = 'app')
    {
        $paths = $this->make('paths');
        if (array_key_exists($path, $paths)) {
            return $paths[$path];
        }
        if ($cPath = Facades\Config::get("paths.$path")) {
            return $this->path() . $cPath;
        }
        throw new OctophpException("Cannot find path for key '$path'.");
    }

}