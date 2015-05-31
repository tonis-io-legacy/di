<?php

namespace Tonis\Di\Generator;

use Tonis\Di\Annotation;
use Tonis\Di\Metadata\MetadataInterface;

class ArrayGenerator implements GeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(MetadataInterface $metadata)
    {
        return [
            $metadata->getClassName(),
            $this->buildConstructor($metadata),
            $this->buildMethods($metadata)
        ];
    }

    /**
     * @param MetadataInterface $metadata
     * @return array
     */
    private function buildConstructor(MetadataInterface $metadata)
    {
        $constructor = [];
        foreach ($metadata->getConstructor() as $annotation) {
            $value = $this->prepareValueFromAnnotation($annotation);

            if ($value === null) {
                continue;
            }

            $constructor[] = $value;
        }

        return $constructor;
    }

    /**
     * @param MetadataInterface $metadata
     * @return array
     */
    private function buildMethods(MetadataInterface $metadata)
    {
        $methods = [];
        foreach ($metadata->getMethods() as $methodName => $annotations) {
            foreach ($annotations as $annotation) {
                $value = $this->prepareValueFromAnnotation($annotation);

                if ($value === null) {
                    continue;
                }

                $methods[$methodName] = $value;
            }
        }

        return $methods;
    }

    /**
     * @param mixed $annotation
     * @return null|string
     */
    private function prepareValueFromAnnotation($annotation)
    {
        if ($annotation instanceof Annotation\Inject) {
            return '@' . $annotation->value;
        } elseif ($annotation instanceof Annotation\Param) {
            return '$' . $annotation->value;
        }
        return null;
    }
}
