<?php

namespace Tonis\Di\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Tonis\Di\Annotation;

final class MetadataFactory
{
    /** @var ClassMetadata[] */
    private $loadedMetadata;
    /** @var \Doctrine\Common\Annotations\AnnotationReader */
    private $reader;

    public function __construct()
    {
        $this->reader = new AnnotationReader();

        foreach (glob(__DIR__ . '/../Annotation/*.php') as $file) {
            AnnotationRegistry::registerFile($file);
        }
    }

    /**
     * @param string $className
     * @return ClassMetadata
     */
    public function getMetadataForClass($className)
    {
        if (!isset($this->loadedMetadata[$className])) {
            $this->loadMetadata($className);
        }
        return $this->loadedMetadata[$className];
    }

    /**
     * @param string $className
     * @throws Exception\InvalidComponentException
     */
    private function loadMetadata($className)
    {
        $md = new ClassMetadata($className);
        $reflClass = $md->getReflectionClass();

        $component = $this->reader->getClassAnnotation($reflClass, 'Tonis\\Di\\Annotation\\Component');

        if (!$component instanceof Annotation\Component) {
            throw new Exception\InvalidComponentException(sprintf(
                'Class "%s" is not an injectable component: did you forget the @Component annotation?',
                $className
            ));
        }

        if (null === $component->name) {
            $md->setName($className);
        } else {
            $md->setName($component->name);
        }

        $this->loadMetadataMethods($reflClass, $md);

        $this->loadedMetadata[$className] = $md;
    }

    /**
     * @param \ReflectionClass $reflClass
     * @param ClassMetadata $md
     */
    private function loadMetadataMethods(\ReflectionClass $reflClass, ClassMetadata $md)
    {
        /** @var \ReflectionMethod $reflMethod */
        foreach ($reflClass->getMethods() as $reflMethod) {
            $methodAnnotations = $this->reader->getMethodAnnotations($reflMethod);

            /** @var \Tonis\Di\Annotation\Method $annotation */
            foreach ($methodAnnotations as $annotation) {
                if ($reflMethod->isConstructor()) {
                    $md->setConstructor($annotation->params);
                    continue;
                }

                $md->addMethod($reflMethod->name, $annotation->params);
            }
        }
    }
}
