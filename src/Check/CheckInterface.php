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
interface CheckInterface
{
    public function getId(): string;

    /**
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

    public function getGroup(): string;

    public function setGroup(string $group);

    public function setAdditionParams(array $data);
}
