<?php

namespace Tonis\Di;

/**
 * @coversDefaultClass \Tonis\Di\ContainerUtil
 */
class ContainerUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::get
     */
    public function testGetReturnsInputIfNothingMatches()
    {
        $this->assertSame('foobar', ContainerUtil::get(new Container(), 'foobar'));
    }

    /**
     * @covers ::get
     */
    public function testGetReturnsClassIfServiceDoesNotExist()
    {
        $result = ContainerUtil::get(new Container(), 'Tonis\Di\Container');
        $this->assertInstanceOf('Tonis\Di\Container', $result);
    }

    /**
     * @covers ::get
     */
    public function testGetReturnsServiceIfItExists()
    {
        $obj = new \StdClass();
        $i = new Container();
        $i->set('foo', $obj);

        $this->assertSame($obj, ContainerUtil::get($i, 'foo'));
    }
}
