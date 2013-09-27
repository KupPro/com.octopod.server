<?php

namespace Octopod\Octophp;

class Uri {

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function base()
    {
        return
            $this->request
            ->getSymfonyRequest()
            ->getSchemeAndHttpHost() .

            $this->request
            ->getSymfonyRequest()
            ->getBasePath();
    }

    public function create($uri = '')
    {
        return $this->base().'/'.ltrim($uri, '/');
    }

}