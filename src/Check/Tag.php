<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\VirtualProperty(
 *     exp="object.count()",
 *     options={@JMS\SerializedName("count")}
 *  )
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Tag implements \ArrayAccess, \Iterator, \Countable
{
    use CheckArraybleTrait;

    /**
     * @JMS\SerializedName("id")
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $id;

    /**
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     *
     * @var string
     */
    protected $name;

    /**
     * @JMS\SerializedName("descr")
     * @JMS\Type("string")
     * @JMS\SkipWhenEmpty()
     *
     * @var ?string
     */
    protected $descr;

    public function __construct(string $id, ?string $name = null, ?string $descr = null)
    {
        $this->id = $id;
        $this->name = null === $name ? $id : $name;
        $this->descr = $descr;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param mixed $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $descr
     *
     * @return $this
     */
    public function setDescr(?string $descr): self
    {
        $this->descr = $descr;

        return $this;
    }

    public function getDescr(): ?string
    {
        return $this->descr;
    }

    /**
     * @param CheckInterface|Proxy $check
     *
     * @return $this
     */
    public function addCheck(string $checkId, &$check): self
    {
        $this->checks[$checkId] = &$check;

        return $this;
    }

    /**
     * @JMS\SerializedName("label")
     * @JMS\Type("string")
     * @JMS\VirtualProperty()
     *
     * @return string
     */
    public function getLabel()
    {
        return sprintf('%s(%d)', $this->name, $this->count());
    }

    /**
     * @JMS\SerializedName("checks")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("array<string>")
     * @JMS\VirtualProperty()
     *
     * @return string[]
     */
    public function getCheckIds(): array
    {
        return array_keys($this->checks);
    }
}
