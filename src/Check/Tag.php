<?php

namespace Tvi\MonitorBundle\Check;

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
