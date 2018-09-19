<?php

namespace Tvi\MonitorBundle\Check;

trait ArraybleProxyTrait // implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var CheckInterface[]
     */
    protected $checks = [];

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->checks);
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->checks[$offset]);
    }

    /**
     * @param string $offset
     *
     * @return CheckInterface
     */
    public function offsetGet($offset)
    {
        if($this->checks[$offset] instanceof Proxy) {
            $this->checks[$offset] = $this->checks[$offset]();
        }
        return $this->checks[$offset];
    }

    /**
     * @param string $offset
     * @param CheckInterface $value
     */
    public function offsetSet(/** @scrutinizer ignore-unused */ $offset, /** @scrutinizer ignore-unused */$value)
    {
    }

    /**
     * @param string $offset
     *
     * @return CheckInterface
     */
    public function offsetUnset($offset)
    {
        unset($this->checks[$offset]);
    }

    /**
     * @return CheckInterface
     */
    public function current()
    {
        $key = $this->key();

        if($this->checks[$key] instanceof Proxy) {
            $this->checks[$key] = $this->checks[$key]();
        }

        return $this->checks[$key];
    }

    /**
     * @return CheckInterface
     */
    public function next()
    {
        return next($this->checks);
    }

    /**
     * @return string
     */
    public function key()
    {
        return key($this->checks);
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return (boolean)key($this->checks);
    }

    /**
     *
     */
    public function rewind()
    {
        reset($this->checks);
    }

    /**
     * @return CheckInterface[]
     */
    public function toArray()
    {
        $out =  [];
        foreach ($this as $k => $v) {
            $out[$k] = $v;
        }
        return $out;
    }
}
