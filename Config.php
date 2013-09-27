<?php

namespace Octopod\Octophp;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\FileLoader;
use Illuminate\Config\Repository;

class Config {

    /** @var Filesystem */
    protected $filesystem;

    /** @var array */
    protected $repositories = array();

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function addRepository($path)
    {
        $loader = new FileLoader($this->filesystem, $path);
        $repository = new Repository($loader, Octopod::env());

        array_push($this->repositories, $repository);
    }

    public function get($key, $default = null, $config = 'app')
    {
        $key = "$config.$key";
        $value = $default;

        /** @var Repository $repository */
        foreach ($this->repositories as $repository) {
            if ($repository->has($key)) {
                $value = $repository->get($key);
                if ($value instanceof \Closure) {
                    $value = $value();
                }
                break;
            }
        }

        return $value;
    }

    /**
     * Alias config files to method names
     *
     * @param $name - config filename
     * @param $args - $key, $default
     * @return mixed
     */
    public function __call($name, $args){
        list($key, $default) = $args;
        return $this->get($key, $default, $name);
    }

}