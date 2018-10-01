<?php

/*
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Tag implements \ArrayAccess, \Iterator, \Countable
{
    use CheckArraybleTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string$checkName
     * @param CheckInterface $check
     */
    public function addCheck($checkName, &$check)
    {
        $this->checks[$checkName] = &$check;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return sprintf('%s (%d)', $this->name, $this->count());
    }

    /**
     * @return string[]
     */
    public function getChecknames(): array
    {
        return array_keys($this->checks);
    }
}
