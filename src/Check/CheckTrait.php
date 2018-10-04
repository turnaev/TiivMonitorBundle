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

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait CheckTrait
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string[]
     */
    protected $tags = [];

    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $descr;

    /**
     * @var ?string
     */
    protected $label;

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     *
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @return $this
     */
    public function setGroup(string $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Return a label describing this test instance.
     *
     * @return string
     */
    public function getLabel()
    {
        if (null !== $this->label) {
            return $this->label;
        }

        return sprintf('Check %s', $this->id);
    }

    /**
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getDescr(): ?string
    {
        return $this->descr;
    }

    /**
     * @return $this
     */
    public function setDescr(?string $descr)
    {
        $this->descr = $descr;

        return $this;
    }

    public function setAdditionParams(array $data)
    {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }

        if (array_key_exists('group', $data)) {
            $this->setGroup($data['group']);
        }

        if (array_key_exists('tags', $data)) {
            $this->setTags($data['tags']);
        }

        if (array_key_exists('label', $data)) {
            $this->setLabel($data['label']);
        }

        if (array_key_exists('descr', $data)) {
            $this->setDescr($data['descr']);
        }
    }
}
