<?php

namespace Tonis\Di\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
final class Param implements AnnotationInterface, MethodAnnotationInterface
{
    /** @var string */
    public $value;
}
