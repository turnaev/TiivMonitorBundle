<?php

namespace Tvi\MonitorBundle\Check;

use ZendDiagnostics\Check\CheckCollectionInterface;
use ZendDiagnostics\Check\CheckInterface;

class Tag implements \ArrayAccess, \Iterator, \Countable
{
    use ArraybleProxyTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param string $name
     * @param string $title
     */
    public function __construct(string $name, ?string $title = null)
    {
        $this->name = $name;
        $this->title = $title !== null ? $title : $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string$checkName
     * @param CheckCollectionInterface|CheckInterface $check
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
        return sprintf('%s (%d)', $this->title, $this->count());
    }
}
