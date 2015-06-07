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

    /**
     * @var Container
     */
    protected $i;

    /**
     * @covers ::set
     * @covers \Tonis\Di\Exception\ServiceExistsException::__construct
     * @expectedException \Tonis\Di\Exception\ServiceExistsException
     * @expectedExceptionMessage The service with name "foo" already exists
     */
    public function testSetThrowsExceptionForDuplicates()
    {
        $i = $this->i;
        $i->set(
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
        $i = new Container();
        $this->assertFalse($i->has('foo'));

        $i->set('foo', null);
        $this->assertTrue($i->has('foo'));

        $i->set('bar', 'bar');
        $this->assertTrue($i->has('bar'));
    }

    /**
     * @covers ::set
     */
    public function testSet()
    {
        $i = new Container();
        $i->set('foo', 'foo');
        $i->set('bar', 'bar');

        $refl = new \ReflectionClass($i);
        $prop = $refl->getProperty('specs');
        $prop->setAccessible(true);

        $specs = $prop->getValue($i);
        $this->assertCount(2, $specs);
        $this->assertSame('foo', $specs['foo']);
        $this->assertSame('bar', $specs['bar']);
    }

    /**
     * @covers ::decorate
     */
    public function testDecorate()
    {
        $i = $this->i;
        $i->decorate(
            'foo',
            function () {
            }
        );
        $i->decorate(
            'bar',
            function () {
            }
        );

        $refl = new \ReflectionClass($i);
        $prop = $refl->getProperty('decorators');
        $prop->setAccessible(true);

        $specs = $prop->getValue($i);
        $this->assertCount(2, $specs);
    }

    /**
     * @covers ::wrap
     */
    public function testWrap()
    {
        $i = $this->i;
        $i->wrap(
            'foo',
            function () {
            }
        );
        $i->wrap(
            'bar',
            function () {
            }
        );

        $refl = new \ReflectionClass($i);
        $prop = $refl->getProperty('wrappers');
        $prop->setAccessible(true);

        $specs = $prop->getValue($i);
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
        $i = $this->i;

        $i->offsetSet('foo', 'bar');
        $this->assertTrue($i->offsetExists('foo'));
        $this->assertSame('bar', $i->offsetGet('foo'));

        $i->offsetUnset('foo');
        $this->assertFalse($i->offsetExists('foo'));
    }

    /**
     * @covers ::get
     * @covers \Tonis\Di\Exception\RecursiveDependencyException
     * @expectedException \Tonis\Di\Exception\RecursiveDependencyException
     * @expectedExceptionMessage Dependency recursion detected for "recursion": "recursion->recursion"
     */
    public function testGetThrowsExceptionForRecursion()
    {
        $i = $this->i;
        $i->set('recursion', function (Container $i) {
            return $i->get('recursion');
        });
        $i->get('recursion');
    }

    /**
     * @covers ::get
     * @covers \Tonis\Di\Exception\ServiceDoesNotExistException
     * @expectedException \Tonis\Di\Exception\ServiceDoesNotExistException
     * @expectedExceptionMessage The service with name "doesnotexist" does not exist
     */
    public function testGetThrowsExceptionForMissingService()
    {
        $this->i->get('doesnotexist');
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesFromStringsIfClassExists()
    {
        $i = $this->i;
        $i->set('class', TestFactory::class);
        $result = $i->get('class');

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
        $i = $this->i;
        $i->set('foob', 'Tonis\Di\TestAsset\DoesNotExist');
        $result = $i->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesWithObjectReturnExpectedObject()
    {
        $i = $this->i;
        $i->set('foob', new \StdClass());
        $result = $i->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     */
    public function testGetCreatesServicesWithClosureReturnExpectedObject()
    {
        $i = $this->i;
        $i->set(
            'foob',
            function (Container $container) {
                return new \StdClass();
            }
        );
        $result = $i->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     */
    public function testGetReturnsEarlyWhenServiceExists()
    {
        $value = new \StdClass();

        $i = $this->i;
        $i->set('early', $value);

        $first = $i->get('early');
        $second = $i->get('early');

        $this->assertSame($value, $first);
        $this->assertSame($second, $first);
    }

    /**
     * @covers ::get
     */
    public function testGetUnsetSpecOnceComplete()
    {
        $i = new Container();
        $i->set(
            'foo',
            function () {
            }
        );
        $i->set(
            'bar',
            function () {
            }
        );
        $i->set(
            'baz',
            function () {
            }
        );

        $refl = new \ReflectionClass($i);
        $prop = $refl->getProperty('specs');
        $prop->setAccessible(true);

        $this->assertCount(3, $prop->getValue($i));

        $i->get('foo');
        $this->assertCount(2, $prop->getValue($i));

        $i->get('bar');
        $this->assertCount(1, $prop->getValue($i));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesClosures()
    {
        $i = $this->i;
        $i->set('closure', function () {
            return 'closure';
        });
        $this->assertSame('closure', $i->get('closure'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesObjects()
    {
        $object = new \StdClass();

        $i = $this->i;
        $i->set('object', $object);
        $this->assertSame($object, $i->get('object'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesClassStrings()
    {
        $i = $this->i;
        $i->set('stdclass', new \StdClass);
        $this->assertInstanceOf('StdClass', $i->get('stdclass'));
    }

    /**
     * @covers ::create
     */
    public function testCreateHandlesServiceFactories()
    {
        $i = $this->i;
        $i->set('factory', new TestAsset\TestFactory());
        $this->assertInstanceOf('StdClass', $i->get('factory'));
    }

    /**
     * @covers ::create
     * @covers \Tonis\Di\Exception\InvalidServiceException::__construct
     * @expectedException \Tonis\Di\Exception\InvalidServiceException
     * @expectedExceptionMessage Creating service "invalid" failed: "1" was an invalid spec
     */
    public function testCreateThrowsExceptionForInvalidServiceSpec()
    {
        $i = $this->i;
        $i->set('invalid', true);
        $i->get('invalid');
    }

    /**
     * @covers ::create
     * @covers ::wrapService
     */
    public function testWrappersModifiesOriginalInstance()
    {
        $object = new \StdClass();
        $i = $this->i;
        $i->set('wrapper', $object);
        $i->wrap('wrapper', function (Container $i, $name, $callable) {
            $object = $callable();
            $object->foo = 'bar';

            return $object;
        });

        $result = $i->get('wrapper');
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
        $i = $this->i;
        $i->set('wrapper', $object);
        $i->wrap('wrapper', function () {
            return [];
        });

        $result = $i->get('wrapper');
        $this->assertInternalType('array', $result);
    }

    /**
     * @covers ::create
     * @covers ::wrapService
     */
    public function testServiceWrappersReturnNewInstance()
    {
        $object = new \StdClass();
        $i = $this->i;
        $i->set('wrapper', $object);
        $i->wrap('wrapper', new TestWrapper());

        $result = $i->get('wrapper');
        $this->assertSame($object, $result->original);
        $this->assertSame('wrapper', $result->name);
        $this->assertTrue($result->didItWork);
    }

    /**
     * @covers ::wrapService
     */
    public function testServiceWrappersWithNoWrapperReturnsNull()
    {
        $i = $this->i;
        $i->set('class', new TestFactory());
        $result = $i->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::create
     * @covers ::decorateService
     */
    public function testCallableDecoratorsModifyInstance()
    {
        $object = new \StdClass();
        $i = $this->i;
        $i->set('decorate', $object);
        $i->decorate('decorate', function (Container $i, \StdClass $obj) {
            $obj->foo = 'bar';

            return $obj;
        });

        $result = $i->get('decorate');
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
        $i = $this->i;
        $i->set('decorate', $object);
        $i->decorate('decorate', new TestDecorator());

        $result = $i->get('decorate');
        $this->assertSame($object, $result);
        $this->assertTrue($result->didItWork);
    }

    /**
     * @covers ::decorateService
     */
    public function testServiceDecoratorsWithNoDecoratorReturns()
    {
        $i = $this->i;
        $i->set('class', new TestFactory());
        $result = $i->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    protected function setUp()
    {
        $i = $this->i = new Container();
        $i->set('foo', new \StdClass());
        $i->set('bar', new \StdClass());
    }
}
