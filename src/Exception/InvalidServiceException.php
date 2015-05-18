<?php

namespace Tonis\Di\Exception;

class InvalidServiceException extends \InvalidArgumentException
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(sprintf(
            'Creating service "%s" failed: the service spec is invalid',
            $name
        ));
    }
}
