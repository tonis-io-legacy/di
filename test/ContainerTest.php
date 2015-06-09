<?php

namespace Tonis\Di;

use Tonis\Di\TestAsset\TestDecorator;
use Tonis\Di\TestAsset\TestFactory;
use Tonis\Di\TestAsset\TestWrapper;

/**
 * @coversDefaultClass \Tonis\Di\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Container */
    protected $di;

    /**
     * @covers ::set
     * @covers \Tonis\Di\Exception\ServiceExistsException::__construct
     * @expectedException \Tonis\Di\Exception\ServiceExistsException
     * @expectedExceptionMessage The service with name "foo" already exists
     */
    public function testSetThrowsExceptionForDuplicates()
    {
        $di = $this->di;
        $di->set(
            'foo',
            function () {
            }
        );
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $di = new Container();
        $this->assertFalse($di->has('foo'));

        $di->set('foo', null);
        $this->assertTrue($di->has('foo'));

        $di->set('bar', 'bar');
        $this->assertTrue($di->has('bar'));
    }

    /**
     * @covers ::set
     * @covers \Tonis\Di\Exception\ServiceExistsException::__construct
     * @expectedException \Tonis\Di\Exception\ServiceExistsException
     * @expectedExceptionMessage The service with name "foo" already exists
     */
    public function testSet()
    {
        $di = new Container();
        $di->set('foo', 'foo', true);
        $di->set('foo', 'foo', true);
    }

    /**
     * @covers ::set
     */
    public function testRawSet()
    {
        $this->di->set('class', TestFactory::class, true);
        $this->assertSame(TestFactory::class, $this->di->get('class'));
    }

    /**
     * @covers ::set
     */
    public function testRawSetThrowsExceptionOnDupes()
    {
        $this->di->set('class', TestFactory::class, true);
        $this->assertSame(TestFactory::class, $this->di->get('class'));
    }

    /**
     * @covers ::decorate
     */
    public function testDecorate()
    {
        $di = $this->di;
        $di->decorate(
            'foo',
            function () {
            }
        );
        $di->decorate(
            'bar',
            function () {
            }
        );

        $refl = new \ReflectionClass($di);
        $prop = $refl->getProperty('decorators');
        $prop->setAccessible(true);

        $specs = $prop->getValue($di);
        $this->assertCount(2, $specs);
    }

    /**
     * @covers ::wrap
     */
    public function testWrap()
    {
        $di = $this->di;
        $di->wrap(
            'foo',
            function () {
            }
        );
        $di->wrap(
            'bar',
            function () {
            }
        );

        $refl = new \ReflectionClass($di);
        $prop = $refl->getProperty('wrappers');
        $prop->setAccessible(true);

        $specs = $prop->getValue($di);
        $this->assertCount(2, $specs);
    }

    /**
     * @covers ::offsetExists
     * @covers ::offsetGet
     * @covers ::offsetSet
     * @covers ::offsetUnset
     */
    public function testArrayAccess()
    {
        $di = $this->di;

        $di->offsetSet('foo', 'bar');
        $this->assertTrue($di->offsetExists('foo'));
        $this->assertSame('bar', $di->offsetGet('foo'));

        $di->offsetUnset('foo');
        $this->assertFalse($di->offsetExists('foo'));
    }

    /**
     * @covers ::get
     * @covers \Tonis\Di\Exception\RecursiveDependencyException
     * @expectedException \Tonis\Di\Exception\RecursiveDependencyException
     * @expectedExceptionMessage Dependency recursion detected for "recursion": "recursion->recursion"
     */
    public function testGetThrowsExceptionForRecursion()
    {
        $di = $this->di;
        $di->set('recursion', function (Container $di) {
            return $di->get('recursion');
        });
        $di->get('recursion');
    }

    /**
     * @covers ::get
     * @covers \Tonis\Di\Exception\ServiceDoesNotExistException
     * @expectedException \Tonis\Di\Exception\ServiceDoesNotExistException
     * @expectedExceptionMessage The service with name "doesnotexist" does not exist
     */
    public function testGetThrowsExceptionForMissingService()
    {
        $this->di->get('doesnotexist');
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesFromStringsIfClassExists()
    {
        $di = $this->di;
        $di->set('class', TestFactory::class);
        $result = $di->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     * @covers \Tonis\Di\Exception\InvalidServiceException
     * @expectedException \Tonis\Di\Exception\InvalidServiceException
     * @expectedExceptionMessage Creating service "foob" failed: "Tonis\Di\TestAsset\DoesNotExist" was an invalid spec
     */
    public function testGetCreatesServicesWithNoValidOptionThrowsExpectedException()
    {
        $di = $this->di;
        $di->set('foob', 'Tonis\Di\TestAsset\DoesNotExist');
        $result = $di->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesWithObjectReturnExpectedObject()
    {
        $di = $this->di;
        $di->set('foob', new \StdClass());
        $result = $di->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesWithClosureReturnExpectedObject()
    {
        $di = $this->di;
        $di->set(
            'foob',
            function (Container $container) {
                return new \StdClass();
            }
        );
        $result = $di->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     */
    public function testGetReturnsEarlyWhenServiceExists()
    {
        $value = new \StdClass();

        $di = $this->di;
        $di->set('early', $value);

        $first = $di->get('early');
        $second = $di->get('early');

        $this->assertSame($value, $first);
        $this->assertSame($second, $first);
    }

    /**
     * @covers ::get
     */
    public function testGetUnsetSpecOnceComplete()
    {
        $di = new Container();
        $di->set(
            'foo',
            function () {
            }
        );
        $di->set(
            'bar',
            function () {
            }
        );
        $di->set(
            'baz',
            function () {
            }
        );

        $refl = new \ReflectionClass($di);
        $prop = $refl->getProperty('specs');
        $prop->setAccessible(true);

        $this->assertCount(3, $prop->getValue($di));

        $di->get('foo');
        $this->assertCount(2, $prop->getValue($di));

        $di->get('bar');
        $this->assertCount(1, $prop->getValue($di));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesClosures()
    {
        $di = $this->di;
        $di->set('closure', function () {
            return 'closure';
        });
        $this->assertSame('closure', $di->get('closure'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesObjects()
    {
        $object = new \StdClass();

        $di = $this->di;
        $di->set('object', $object);
        $this->assertSame($object, $di->get('object'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesClassStrings()
    {
        $di = $this->di;
        $di->set('stdclass', new \StdClass);
        $this->assertInstanceOf('StdClass', $di->get('stdclass'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesServiceFactories()
    {
        $di = $this->di;
        $di->set('factory', new TestAsset\TestFactory());
        $this->assertInstanceOf('StdClass', $di->get('factory'));
    }

    /**
     * @covers ::create
     * @covers \Tonis\Di\Exception\InvalidServiceException::__construct
     * @expectedException \Tonis\Di\Exception\InvalidServiceException
     * @expectedExceptionMessage Creating service "invalid" failed: "1" was an invalid spec
     */
    public function testCreateThrowsExceptionForInvalidServiceSpec()
    {
        $di = $this->di;
        $di->set('invalid', true);
        $di->get('invalid');
    }

    /**
     * @covers ::create
     * @covers ::wrapService
     */
    public function testWrappersModifiesOriginalInstance()
    {
        $object = new \StdClass();
        $di = $this->di;
        $di->set('wrapper', $object);
        $di->wrap('wrapper', function (Container $i, $name, $callable) {
            $object = $callable();
            $object->foo = 'bar';

            return $object;
        });

        $result = $di->get('wrapper');
        $this->assertSame($object, $result);
        $this->assertSame('bar', $object->foo);
    }

    /**
     * @covers ::create
     * @covers ::wrapService
     */
    public function testCallableWrappersReturnNewInstance()
    {
        $object = new \StdClass();
        $di = $this->di;
        $di->set('wrapper', $object);
        $di->wrap('wrapper', function () {
            return [];
        });

        $result = $di->get('wrapper');
        $this->assertInternalType('array', $result);
    }

    /**
     * @covers ::create
     * @covers ::wrapService
     */
    public function testServiceWrappersReturnNewInstance()
    {
        $object = new \StdClass();
        $di = $this->di;
        $di->set('wrapper', $object);
        $di->wrap('wrapper', new TestWrapper());

        $result = $di->get('wrapper');
        $this->assertSame($object, $result->original);
        $this->assertSame('wrapper', $result->name);
        $this->assertTrue($result->didItWork);
    }

    /**
     * @covers ::wrapService
     */
    public function testServiceWrappersWithNoWrapperReturnsNull()
    {
        $di = $this->di;
        $di->set('class', new TestFactory());
        $result = $di->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::create
     * @covers ::decorateService
     */
    public function testCallableDecoratorsModifyInstance()
    {
        $object = new \StdClass();
        $di = $this->di;
        $di->set('decorate', $object);
        $di->decorate('decorate', function (Container $i, \StdClass $obj) {
            $obj->foo = 'bar';

            return $obj;
        });

        $result = $di->get('decorate');
        $this->assertSame($object, $result);
        $this->assertSame('bar', $object->foo);
    }

    /**
     * @covers ::create
     * @covers ::decorateService
     */
    public function testServiceDecoratorsModifyInstance()
    {
        $object = new \StdClass();
        $object->value = __FUNCTION__;
        $di = $this->di;
        $di->set('decorate', $object);
        $di->decorate('decorate', new TestDecorator());

        $result = $di->get('decorate');
        $this->assertSame($object, $result);
        $this->assertTrue($result->didItWork);
    }

    /**
     * @covers ::decorateService
     */
    public function testServiceDecoratorsWithNoDecoratorReturns()
    {
        $di = $this->di;
        $di->set('class', new TestFactory());
        $result = $di->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    protected function setUp()
    {
        $di = $this->di = new Container();
        $di->set('foo', new \StdClass());
        $di->set('bar', new \StdClass());
    }
}
