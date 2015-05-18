<?php
 
namespace Tonis\Di\Finder;

/**
 * @coversDefaultClass \Tonis\Di\Finder\ComponentFinder
 */
class ComponentFinderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @todo This test could be better.
     * @covers ::__construct
     */
    public function testFinder()
    {
        $finder = new ComponentFinder();
        $finder->in(__DIR__ . '/../TestAsset');
        
        $this->assertCount(2, $finder);
    }
}
