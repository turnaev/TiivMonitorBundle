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
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait CheckTrait
{
    /**
     * @JMS\SerializedName("id")
     * @JMS\Expose()
     *
     * @var string
     */
    protected $id;

    /**
     * @var ?string
     */
    protected $label;

    /**
     * @JMS\SerializedName("group")
     * @JMS\Type("string")
     * @JMS\Expose()
     *
     * @var string
     */
    protected $group;

    /**
     * @JMS\SerializedName("descr")
     * @JMS\Type("string")
     * @JMS\SkipWhenEmpty()
     * @JMS\Expose()
     *
     * @var string?
     */
    protected $descr;

    /**
     * @JMS\SerializedName("importance")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\Expose()
     *
     * @var ?string
     */
    protected $importance;

    /**
     * @JMS\SerializedName("tags")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("array<string>")
     * @JMS\Expose()
     *
     * @var string[]
     */
    protected $tags = [];

    /**
     * Perform the actual check and return a ResultInterface.
     *
     * @return ResultInterface
     */
    abstract public function check();

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }


    public function getImportance()
    {
        return $this->importance;
    }


    public function setImportance($importance)
    {
        $this->importance = $importance;
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
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        $this->tags = array_unique($this->tags);
    }

    /**
     * @param string $tag
     */
    public function addTag($tag)
    {
        $this->tags[$tag];
        $this->tags = array_unique($this->tags);
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group)
    {
        $this->group = $group;
    }

    /**
     * @JMS\SerializedName("label")
     * @JMS\Type("string")
     * @JMS\VirtualProperty()
     * @JMS\Expose()
     */
    public function getLabel(): string
    {
        if (null !== $this->label) {
            return $this->label;
        }

        return sprintf('Check %s', $this->id);
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return ?string
     */
    public function getDescr(): ?string
    {
        return $this->descr;
    }

    public function setDescr(?string $descr)
    {
        $this->descr = $descr;
    }

    public function setAdditionParams(array $data)
    {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
        }

        if (array_key_exists('group', $data)) {
            $this->setGroup($data['group']);
        }

        if (array_key_exists('importance', $data)) {
            $this->setImportance($data['importance']);
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
