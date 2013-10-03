<?php

namespace Octopod\Octophp\Log;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log {

    /** @var \Monolog\Logger  */
    protected $logger;

    /** @var \Monolog\Logger  */
    protected $loggerRequests;

    public function __construct()
    {
        /*$this->logger = new Logger('log');
        $this->logger->pushHandler(new StreamHandler(App::path('storage').'/', Logger::DEBUG));*/

        $this->logger = new Logger('log');
        $this->logger->pushHandler(new StreamHandler(\App::path('storage').'/log/requests.txt', Logger::DEBUG));
    }

    /**
     * @param $data — json string sent by mobile client
     */
    public function request($data)
    {
        $this->logger->addDebug($data);
    }

}