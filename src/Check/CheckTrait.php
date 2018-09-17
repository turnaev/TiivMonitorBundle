<?php

namespace Tvi\MonitorBundle\Check;

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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
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

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function setGroup(string $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @param null|string $group
     * @param array|null  $tags
     */
    public function setAdditionParams(array $data)
    {
        if(isset($data['id'])) {
            $this->setId($data['id']);
        }

        if(isset($data['group'])) {
            $this->setGroup($data['group']);
        }

        if(isset($data['tags'])) {
            $this->setTags($data['tags']);
        }

        if(isset($data['label'])) {
            $this->setLabel($data['label']);
        }
    }
}
