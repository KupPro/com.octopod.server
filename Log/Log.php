<?php

namespace Octopod\Octophp\Log;

use Illuminate\Support\Facades\Config;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;


class Log
{

    protected $log;
    protected $request;

    protected $levels = array(
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency',
    );


//    public function __construct()
//    {
////        $this->log = new Logger('log');
////        $this->log->pushHandler(new RotatingFileHandler(\App::path('log').'', 0));
////
////        $this->request = new Logger('request');
////        $handler = new RotatingFileHandler(\App::path('storage').'/log/request.txt', 0);
////        $handler->setFormatter(new JsonFormatter());
////        $this->request->pushHandler($handler);
//    }

//    public function request()
//    {
//        $info = \Request::info();
//        $this->request->addInfo('request', $info);
//    }

    public function prepareLog($level, $type, $text)
    {
        if (\Config::get("logTypes." . $type . "." . \Config::get("mode")))
            $this->saveLog($level, $type, $text, \Config::get("logTypes." . $type . "." . \Config::get("mode")));
        else
            $this->saveLog($level, "unknown", $text, \Config::get("logTypes.unknown." . \Config::get("mode")));
    }

    public function saveLog($level, $type, $text, $mask)
    {
        if ($mask & OCTOLOG_TEXT) {
            $this->pushTextLog($level, $type, $text);
        }
        if ($mask & OCTOLOG_ALERT) {
            $this->pushAlertLog($level, $type, $text);
        }
        if ($mask & OCTOLOG_MAIL) {
            $this->pushMailLog($level, $type, $text);
        }
    }

    public function pushTextLog($level, $type, $text)
    {
        $request = SymfonyRequest::createFromGlobals();

//        print $request->getContent().chr(10).chr(10);
//        print $request->getUri().chr(10).chr(10);


//       print ;
        if (!file_exists(\App::path("log")."/".date('d.m.Y', time())))
            mkdir(\App::path("log")."/".date('d.m.Y', time()));
        file_put_contents(\App::path("log")."/".date('d.m.Y', time())."/".$level."_".$type."_".time().".log",
            '<?php return ' . var_export(array('requestUri'=> $request->getUri(), 'requestRawData' => $request->getContent(), 'log' => $text), true) . ';' );
    }

    public function pushAlertLog($level, $type, $text)
    {
        \Response::addAlert($level . "::" . $type . "::" . $text);
        if ($level == "error") \Response::send();
    }

    public function pushMailLog($level, $type, $text)
    {
        mail(\Config::get("logMail"), "Octopod mail log", "Log level: " . $level . chr(10) . "Log type: " . $type . chr(10) . "Log message: " . $text);
    }

    public function __call($method, $parameters)
    {

        if (in_array($method, $this->levels) && count($parameters) == 2) {
            return $this->prepareLog($method, $parameters[0], $parameters[1]);
        } else {
            return $this->prepareLog("error", "unknown", "Wrong log type or some parameters missing for method [" . $method . "] called with parameters " . print_r($parameters, true));
        }
    }
}