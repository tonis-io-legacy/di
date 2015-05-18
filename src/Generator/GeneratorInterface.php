<?php

namespace Tonis\Di\Generator;

use Tonis\Di\Metadata\MetadataInterface;

interface GeneratorInterface
{
    /**
     * @param \Tonis\Di\Metadata\MetadataInterface $metadata
     * @return mixed
     */
    public function generate(MetadataInterface $metadata);
}
