<?php

namespace Tonis\Di;

/**
 * @coversDefaultClass \Tonis\Di\ContainerAwareTrait
 */
class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::di
     * @covers ::setDi
     */
    public function testSetGetContainer()
    {
        /** @var ContainerAwareTrait $t */
        $t = $this->getObjectForTrait('Tonis\Di\ContainerAwareTrait');
        $this->assertInstanceOf('Tonis\Di\Container', $t->di());

        $di = new Container;
        $t->setDi($di);
        $this->assertSame($di, $t->di());
    }
}
