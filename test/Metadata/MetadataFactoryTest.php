<?php
 
namespace Tonis\Di\Metadata;

use Tonis\Di\Annotation;

/**
 * @coversDefaultClass \Tonis\Di\Metadata\MetadataFactory
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var MetadataFactory */
    private $factory;

    /**
     * @covers ::getMetadataForClass
     * @covers ::loadMetadata
     */
    public function testGetMetadataForUnnamedComponent()
    {
        $f = $this->factory;
        $md = $f->getMetadataForClass('Tonis\Di\TestAsset\UnnamedComponent');
        
        $this->assertSame('Tonis\Di\TestAsset\UnnamedComponent', $md->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::getMetadataForClass
     * @covers ::loadMetadata
     * @covers ::loadMetadataMethods
     */
    public function testGetMetadataForClass()
    {
        $f = $this->factory;
        $md = $f->getMetadataForClass('Tonis\Di\TestAsset\AnnotatedComponent');
        
        $this->assertInstanceOf('Tonis\Di\Metadata\ClassMetadata', $md);
        
        $expected = new ClassMetadata('Tonis\Di\TestAsset\AnnotatedComponent');
        $foo = new Annotation\Inject();
        $foo->value = 'foo';

        $params = new Annotation\Param();
        $params->value = 'params';
        
        $expected->setConstructor([$foo, $params]);
        $expected->setName('inject.test-asset.annotated-component');
        
        $setter = new Annotation\Param();
        $setter->value = 'setter';
        
        $expected->addMethod('setSetter', [$setter]);
        
        $this->assertEquals($expected, $md);
    }

    /**
     * @expectedException \Tonis\Di\Metadata\Exception\InvalidComponentException
     * @expectedExceptionMessage Class "Tonis\Di\TestAsset\ConstructorParams" is not an injectable component
     */
    public function testGetMetadataForClassThrowsExceptionOnInvalidClass()
    {
        $f = $this->factory;
        $f->getMetadataForClass('Tonis\Di\TestAsset\ConstructorParams');
    }
    
    protected function setUp()
    {
        $this->factory = new MetadataFactory();
    }
}
