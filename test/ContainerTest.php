<?php

namespace Tonis\Di;

use Tonis\Di\TestAsset\TestDecorator;
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
    public function testArrayAcces()
    {
        $i = $this->i;

        $i->offsetSet('foo', 'bar');
        $this->assertTrue($i->offsetExists('foo'));
        $this->assertSame('bar', $i->offsetGet('foo'));

        $i->offsetUnset('foo');
        $this->assertFalse($i->offsetExists('foo'));
    }

    /**
     * @covers ::getParamIdentifier
     * @covers ::setParamIdentifier
     */
    public function testParamIdentifier()
    {
        $value = '@@';
        $i = $this->i;
        $i->setParamIdentifier($value);

        $this->assertSame($value, $i->getParamIdentifier());
    }


    /**
     * @covers ::getServiceIdentifier
     * @covers ::setServiceIdentifier
     */
    public function testServiceIdentifier()
    {
        $value = '@@';
        $i = $this->i;
        $i->setServiceIdentifier($value);

        $this->assertSame($value, $i->getServiceIdentifier());
    }

    /**
     * @covers ::get
     * @covers \Tonis\Di\Exception\NullServiceException
     * @expectedException \Tonis\Di\Exception\NullServiceException
     * @expectedExceptionMessage Creating service "null" failed: the service result was null
     */
    public function testGetThrowsExceptionOnNullService()
    {
        $i = $this->i;
        $i->set('null', null);

        $this->assertNull($i->get('null'));
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
     * @covers \Tonis\Di\Exception\RecursiveDependencyException
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
        $i->set('class', 'Tonis\Di\TestAsset\TestFactory');
        $result = $i->get('class');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     * @expectedException \Tonis\Di\Exception\InvalidServiceException
     * @expectedExceptionMessage Creating service "foob" failed: the service spec is invalid
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
     * @expectedException \Tonis\Di\Exception\NullServiceException
     * @expectedExceptionMessage Creating service "foob" failed: the service result was null
     */
    public function testGetCreatesServicesWithNullThrowsExpectedException()
    {
        $i = $this->i;
        $i->set('foob', null);
        $result = $i->get('foob');

        $this->assertInstanceOf('StdClass', $result);
    }

    /**
     * @covers ::get
     * @covers ::create
     * @covers ::createInstanceCallback
     * @covers ::createFromArray
     */
    public function testGetCreatesServicesWithArrayReturnExpectedObject()
    {
        $i = $this->i;
        $i->set('foob', ['\StdClass']);
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
        $i->set('stdclass', 'StdClass');
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
     * @covers ::createFromArray
     * @covers \Tonis\Di\Exception\MissingClassException::__construct
     * @expectedException \Tonis\Di\Exception\MissingClassException
     * @expectedExceptionMessage Class "Missing\Class" does not exist for service "doesnotexist"
     */
    public function testCreateFromArrayThrowsExceptionOnInvalidClass()
    {
        $i = $this->i;
        $i->set('doesnotexist', ['Missing\Class']);
        $i->get('doesnotexist');
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     */
    public function testCreateHandlesArraysCreatesBasicClass()
    {
        $i = $this->i;
        $i->set('array', ['StdClass']);

        $this->assertInstanceOf('StdClass', $i->get('array'));
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     */
    public function testCreateFromArrayCreatesParameterizedClass()
    {
        $i = $this->i;
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    'foogly',
                    'boogly'
                ]
            ]
        );

        $result = $i->get('array');
        $this->assertInstanceOf('Tonis\Di\TestAsset\ConstructorParams', $result);
        $this->assertSame('foogly', $result->getFoo());
        $this->assertSame('boogly', $result->getBar());
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     * @covers ::introspect
     */
    public function testCreateFromArrayCreatesParameterizedClassFromParameters()
    {
        $i = $this->i;
        $i['boogly'] = 'woogly';
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    'foogly',
                    '$boogly'
                ]
            ]
        );

        $result = $i->get('array');
        $this->assertInstanceOf('Tonis\Di\TestAsset\ConstructorParams', $result);
        $this->assertSame('foogly', $result->getFoo());
        $this->assertSame('woogly', $result->getBar());
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     * @covers ::introspect
     */
    public function testCreateFromArrayHandlesNestedParameters()
    {
        $i = $this->i;
        $i['boogly'] = [
            'woogly' => [
                'foogly' => 'zoogly'
            ]
        ];
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    'foogly',
                    '$boogly[woogly][foogly]'
                ]
            ]
        );

        $result = $i->get('array');
        $this->assertInstanceOf('Tonis\Di\TestAsset\ConstructorParams', $result);
        $this->assertSame('foogly', $result->getFoo());
        $this->assertSame('zoogly', $result->getBar());
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     * @covers ::introspect
     * @expectedException \Tonis\Di\Exception\ParameterKeyDoesNotExistException
     * @expectedExceptionMessage The key "[woogly][zoogly]" does not exist in parameter "boogly"
     */
    public function testCreateFromArrayThrowsExceptionWhenMissingKeyForNestedParameter()
    {
        $i = $this->i;
        $i['boogly'] = 'foogly';
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    'foogly',
                    '$boogly[woogly][zoogly]'
                ]
            ]
        );

        $i->get('array');
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     * @covers ::introspect
     */
    public function testCreateHandlesArraysCreatesParameterizedClassFromService()
    {
        $foogly = new \StdClass();
        $i = $this->i;
        $i->set('foogly', $foogly);
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    '@foogly',
                    'boogly'
                ]
            ]
        );

        $result = $i->get('array');
        $this->assertInstanceOf('Tonis\Di\TestAsset\ConstructorParams', $result);
        $this->assertSame($foogly, $result->getFoo());
        $this->assertSame('boogly', $result->getBar());
    }

    /**
     * @covers ::create
     * @covers ::createFromArray
     * @covers ::introspect
     */
    public function testCreateHandlesArraysCreatesParameterizedClassWithSetters()
    {
        $boogly = new \StdClass();

        $i = $this->i;
        $i['foogly'] = 'foogly';
        $i->set('boogly', $boogly);
        $i->set(
            'array',
            [
                'Tonis\Di\TestAsset\ConstructorParams',
                [
                    'foo',
                    'bar'
                ],
                [
                    'setFoo' => '$foogly',
                    'setBar' => '@boogly',
                    'setDoesNotExist' => 'skip'
                ]
            ]
        );

        $result = $i->get('array');
        $this->assertInstanceOf('Tonis\Di\TestAsset\ConstructorParams', $result);
        $this->assertSame('foogly', $result->getFoo());
        $this->assertSame($boogly, $result->getBar());
    }

    /**
     * @covers ::introspect
     * @covers \Tonis\Di\Exception\ParameterDoesNotExistException
     * @expectedException \Tonis\Di\Exception\ParameterDoesNotExistException
     * @expectedExceptionMessage The parameter with name "param" does not exist
     */
    public function testIntrospectThrowsExceptionForInvalidParameter()
    {
        $i = $this->i;
        $i->set('paramexception', ['StdClass', '$param']);
        $i->get('paramexception');
    }

    /**
     * @covers ::create
     * @covers \Tonis\Di\Exception\InvalidServiceException::__construct
     * @expectedException \Tonis\Di\Exception\InvalidServiceException
     * @expectedExceptionMessage Creating service "invalid" failed: the service spec is invalid
     */
    public function testCreateThrowsExceptionForInvalidServiceSpec()
    {
        $i = $this->i;
        $i->set('invalid', true);
        $i->get('invalid');
    }

    /**
     * @covers ::create
     * @covers ::createFroMArray
     */
    public function testCreateFromArrayCreatesServiceIfInstanceIsServiceFactory()
    {
        $i = $this->i;
        $i->set('factory', ['Tonis\Di\TestAsset\TestFactory']);

        $this->assertInstanceOf('StdClass', $i->get('factory'));
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

    protected function setUp()
    {
        $i = $this->i = new Container();
        $i->set('foo', new \StdClass());
        $i->set('bar', new \StdClass());
    }
}
