<?php

namespace Tvi\MonitorBundle\Check;

interface CheckInterface extends \ZendDiagnostics\Check\CheckInterface
{
    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId(string $id);

    /**
     * @return string[]
     */
    public function getTags(): array;

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags);

    /**
     * @return string
     */
    public function getGroup(): string;

    /**
     * @param string $group
     */
    public function setGroup(string $group);

    /**
     * @param array $data
     */
    public function setAdditionParams(array $data);
}
