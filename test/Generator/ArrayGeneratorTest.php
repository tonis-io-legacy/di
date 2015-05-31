<?php

namespace Tonis\Di\Generator;

use Tonis\Di\Annotation;
use Tonis\Di\Metadata\ClassMetadata;
use Tonis\Di\Metadata\MetadataFactory;

/**
 * @coversDefaultClass \Tonis\Di\Generator\ArrayGenerator
 */
class ArrayGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ArrayGenerator */
    private $g;

    /**
     * @covers ::generate
     * @covers ::buildConstructor
     * @covers ::buildMethods
     * @covers ::prepareValueFromAnnotation
     */
    public function testGenerateForValidComponent()
    {
        $g = $this->g;
        
        $mdf = new MetadataFactory();
        $md = $mdf->getMetadataForClass('Tonis\Di\TestAsset\AnnotatedComponent');
        
        $this->assertSame([
            'Tonis\Di\TestAsset\AnnotatedComponent',
            ['@foo', '$params'],
            ['setSetter' => '$setter']
        ], $g->generate($md));
    }

    /**
     * @covers ::generate
     * @covers ::buildConstructor
     * @covers ::buildMethods
     * @covers ::prepareValueFromAnnotation
     */
    public function testGenerateForInvalidComponent()
    {
        $md = new ClassMetadata('Tonis\Di\TestAsset\ConstructorParams');
        $md->setConstructor([new Annotation\Method()]);
        $md->addMethod('setSetter', [new Annotation\Method()]);
        $md->setName('foo');
        
        $g = $this->g;
        
        $this->assertSame(['Tonis\Di\TestAsset\ConstructorParams', [], []], $g->generate($md));
    }
    
    protected function setUp()
    {
        $this->g = new ArrayGenerator();
    }
}
