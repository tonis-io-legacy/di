<?php

namespace Tonis\Di\Metadata;

interface MetadataInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return array
     */
    public function getConstructor();

    /**
     * @return array
     */
    public function getMethods();
}
