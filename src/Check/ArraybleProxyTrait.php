<?php

namespace MonitorBundle\Check;

trait ArraybleProxyTrait // implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * @var CheckInterface[]|CheckCollectionInterface[]
     */
    protected $checks = [];

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
     * @return CheckCollectionInterface|CheckInterface
     */
    public function offsetGet($offset)
    {
        if($this->checks[$offset] instanceof Proxy) {
            $this->checks[$offset] = $this->checks[$offset]();
        }
        return $this->checks[$offset];
    }

    public function offsetSet($offset, $value){}

    /**
     * @param string $offset
     *
     * @return CheckCollectionInterface|CheckInterface
     */
    public function offsetUnset($offset)
    {
        unset($this->checks[$offset]);
    }

    /**
     * @return CheckCollectionInterface|CheckInterface
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
     * @return CheckCollectionInterface|CheckInterface
     */
    public function next()
    {
        return next($this->checks);
    }

    /**
     * @return strin
     */
    public function key()
    {
        return key($this->checks);
    }

    public function valid()
    {
        return key($this->checks);
    }

    /**
     *
     */
    public function rewind()
    {
        reset($this->checks);
    }

    /**
     * @return CheckCollectionInterface[]|CheckInterface[]
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
