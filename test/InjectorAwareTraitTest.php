<?php

namespace Tonis\Di;

/**
 * @covers \Tonis\Di\ContainerAwareTrait
 */
class ContainerAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getContainer, ::setContainer
     */
    public function testSetGetContainer()
    {
        $t = $this->getObjectForTrait('Tonis\Di\ContainerAwareTrait');
        $this->assertInstanceOf('Tonis\Di\Container', $t->dic());
    }
}
