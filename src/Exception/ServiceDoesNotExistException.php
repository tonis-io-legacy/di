<?php

namespace Tonis\Di\Exception;

class ServiceDoesNotExistException extends \InvalidArgumentException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf(
            'The service with name "%s" does not exist',
            $name
        ));
    }
}
