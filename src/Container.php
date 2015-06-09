<?php

namespace Tonis\Di;

use Interop\Container\ContainerInterface;

final class Container implements \ArrayAccess, ContainerInterface
{
    /** @var array */
    protected $params = [];
    /** @var array */
    protected $services = [];
    /** @var array */
    protected $decorators = [];
    /** @var array */
    protected $wrappers = [];
    /** @var array */
    protected $retrieving = [];
    /** @var string */
    protected $parent;
    /** @var array */
    protected $specs = [];

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->services) || array_key_exists($name, $this->specs);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @throws Exception\ServiceExistsException
     */
    public function set($name, $value)
    {
        if (isset($this->specs[$name])) {
            throw new Exception\ServiceExistsException($name);
        }
        $this->specs[$name] = $value;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setService($name, $value)
    {
        $this->services[$name] = $value;
    }

    /**
     * @param string $name
     * @throws Exception\RecursiveDependencyException
     * @throws Exception\ServiceDoesNotExistException
     * @throws Exception\InvalidServiceException
     * @return mixed
     */
    public function get($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        } elseif (in_array($name, $this->retrieving)) {
            throw new Exception\RecursiveDependencyException($name, $this->retrieving);
        } elseif (!array_key_exists($name, $this->specs)) {
            throw new Exception\ServiceDoesNotExistException($name);
        }

        $this->parent = $name;
        $this->retrieving[] = $name;
        $this->services[$name] = $this->create($name);

        unset($this->specs[$name]);
        unset($this->retrieving[$name]);
        $this->parent = null;

        return $this->services[$name];
    }

    /**
     * @param string $name
     * @param mixed $decorator
     */
    public function decorate($name, $decorator)
    {
        $this->decorators[$name][] = $decorator;
    }

    /**
     * @param string $name
     * @param mixed $wrapper
     */
    public function wrap($name, $wrapper)
    {
        $this->wrappers[$name][] = $wrapper;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->params[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->params[$offset] : null;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->params[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->params[$offset]);
    }

    /**
     * @param string $name
     * @return object
     * @throws Exception\InvalidServiceException
     */
    protected function create($name)
    {
        $spec = $this->specs[$name];

        $callback = $this->createInstanceCallback($name, $spec);
        $instance = $this->wrapService($name, $callback);

        if (null === $instance) {
            $instance = $callback();
        }

        $this->decorateService($name, $instance);

        return $instance;
    }

    /**
     * Creates the instance callback which is passed to the wrappers if they exist.
     *
     * @param string $name
     * @param mixed $spec
     * @return \Closure
     */
    private function createInstanceCallback($name, $spec)
    {
        if (is_string($spec) && class_exists($spec)) {
            $spec = new $spec;
        }
        if ($spec instanceof ServiceFactoryInterface) {
            return function () use ($spec) {
                return $spec->createService($this);
            };
        }
        if (is_callable($spec)) {
            return function () use ($spec) {
                return $spec($this);
            };
        }
        if (is_object($spec)) {
            return function () use ($spec) {
                return $spec;
            };
        }
        throw new Exception\InvalidServiceException($name, $spec);
    }

    /**
     * @param string $name
     * @param \Closure $callback
     * @return mixed
     */
    protected function wrapService($name, \Closure $callback)
    {
        if (!isset($this->wrappers[$name])) {
            return null;
        }

        $instance = null;
        foreach ($this->wrappers[$name] as $wrapper) {
            if ($wrapper instanceof ServiceWrapperInterface) {
                $instance = $wrapper->wrapService($this, $name, $callback);
            } else {
                $instance = $wrapper($this, $name, $callback);
            }
        }

        return $instance;
    }

    /**
     * @param string $name
     * @param object $instance
     */
    protected function decorateService($name, $instance)
    {
        if (!isset($this->decorators[$name])) {
            return;
        }

        foreach ($this->decorators[$name] as $decorator) {
            if ($decorator instanceof ServiceDecoratorInterface) {
                $decorator->decorateService($this, $instance);
            } else {
                $decorator($this, $instance);
            }
        }
    }
}
