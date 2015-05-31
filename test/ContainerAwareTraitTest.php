<?php

namespace Tonis\Di;

/**
 * @coversDefaultClass \Tonis\Di\ContainerAwareTrait
 */
class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::di
     */
    public function testSetGetContainer()
    {
        /** @var ContainerAwareTrait $t */
        $t = $this->getObjectForTrait('Tonis\Di\ContainerAwareTrait');
        $this->assertInstanceOf('Tonis\Di\Container', $t->di());
    }
}
