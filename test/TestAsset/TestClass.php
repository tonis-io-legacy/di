<?php

namespace Tonis\Di\TestAsset;

class TestClass
{

    public $var;
    public $arg;

    public function __construct($var = null)
    {
        $this->var = $var;
    }

    public function getClass(\StdClass $i)
    {
        return $i;
    }

    public function setArg($arg)
    {
        $this->arg = $arg;
    }
}
