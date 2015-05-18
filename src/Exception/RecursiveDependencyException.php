<?php

namespace Tonis\Di\Exception;

class RecursiveDependencyException extends \RuntimeException
{
    /**
     * @param string $name
     * @param array $graph
     */
    public function __construct($name, array $graph)
    {
        $graph[] = $name;

        $msg = sprintf(
            'Dependency recursion detected for "%s": "%s"',
            $name,
            implode('->', $graph)
        );
        parent::__construct($msg);
    }
}
