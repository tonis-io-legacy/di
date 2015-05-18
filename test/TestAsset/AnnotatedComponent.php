<?php
 
namespace Tonis\Di\TestAsset;

use Tonis\Di\Annotation as Di;

/**
 * @Di\Component("inject.test-asset.annotated-component")
 */
class AnnotatedComponent
{
    /** @var \StdClass */
    private $foo;
    /** @var array */
    private $params;
    /** @var array */
    private $setter;
    
    /**
     * @Di\Method({@Di\Inject("foo"), @Di\Param("params")})
     */
    public function __construct(\StdClass $foo, array $params)
    {
        $this->foo = $foo;
        $this->params = $params;
    }

    /**
     * @Di\Method({@Di\Param("setter")})
     * 
     * @param array $setter
     */
    public function setSetter($setter)
    {
        $this->setter = $setter;
    }

    /**
     * @return array
     */
    public function getSetter()
    {
        return $this->setter;
    }

    /**
     * @return \StdClass
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}
