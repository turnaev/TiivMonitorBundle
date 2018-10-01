<?php

/*
 * This file is part of the Sonata Project package.
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait CheckArraybleTrait // implements \ArrayAccess, \Iterator, \Countable
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
        return \count($this->checks);
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
        if ($this->checks[$offset] instanceof Proxy) {
            $this->checks[$offset] = $this->checks[$offset]();
        }

        return $this->checks[$offset];
    }

    /**
     * @param string         $offset
     * @param CheckInterface $value
     */
    public function offsetSet($offset, $value)
    {
        $this->checks[$offset] = $value;
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

        return $this->offsetGet($key);
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
     * @return bool
     */
    public function valid()
    {
        return (bool) key($this->checks);
    }

    public function rewind()
    {
        reset($this->checks);
    }

    /**
     * @return CheckInterface[]
     */
    public function toArray()
    {
        $out = [];
        foreach ($this as $k => $v) {
            $out[$k] = $v;
        }

        return $out;
    }
}
