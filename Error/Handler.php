<?php

namespace Octopod\Octophp\Error;

use ErrorException;

class Handler {

    /**
     * @var Displayer
     */
    protected $displayer;

    public function __construct(Displayer $displayer) {
        $this->displayer = $displayer;
    }

    public function register()
    {
        error_reporting(0);
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        $this->registerShutdownHandler();
    }

    protected function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
    }

    protected function registerExceptionHandler()
    {
        set_exception_handler(array($this, 'handleException'));
    }

    protected function registerShutdownHandler()
    {
        register_shutdown_function(array($this, 'handleShutdown'));
    }


    public function handleError($level, $message, $file, $line, $context)
    {
        if (error_reporting() & $level)
        {
            $e = new ErrorException($message, $level, 0, $file, $line);
            $this->handleException($e);
        }
    }

    public function handleException($exception)
    {
        \Log::error($this->displayer->formatException($exception));
        $this->displayer->display($exception);
        $this->halt();
    }

    public function handleShutdown()
    {
        $error = error_get_last();

        if ( ! is_null($error))
        {
            extract($error);
            $e = new ErrorException($message, $type, 0, $file, $line);
            $this->handleException($e);
        }
    }

    protected function halt()
    {
        exit(1);
    }

}