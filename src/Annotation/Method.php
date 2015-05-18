<?php

namespace Tonis\Di\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Method
{
    /** @var array<Tonis\Di\Annotation\MethodAnnotationInterface> */
    public $params = [];
}
