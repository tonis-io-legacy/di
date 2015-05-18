<?php

namespace Tonis\Di\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
final class Inject implements AnnotationInterface, MethodAnnotationInterface
{
    /** @var string */
    public $value;
}
