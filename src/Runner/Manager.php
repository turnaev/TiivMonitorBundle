<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Runner;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Check\Manager as CheckManager;
use Tvi\MonitorBundle\Check\Tag;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Manager
{
    /**
     * @var CheckManager
     */
    protected $checkManager;

    /**
     * @param CheckManager $checkManager
     */
    public function __construct(CheckManager $checkManager)
    {
        $this->checkManager = $checkManager;
    }

    /**
     * @return Runner
     */
    public function getRunner($alias = null, $groups = null, $tags = null): Runner
    {
        $checks = $this->checkManager->findChecks($alias, $groups, $tags);

        $runner = new Runner(null, $checks);

        return $runner;
    }

    /**
     * @param null|string|string[] $alias
     * @param null|string|string[] $groups
     * @param null|string|string[] $tags
     *
     * @return CheckInterface[]
     */
    public function findChecks($alias = null, $groups = null, $tags = null): array
    {
        return $this->checkManager->findChecks($alias, $groups, $tags);
    }

    /**
     * @param null|string|string[] $tags
     *
     * @return Tag[]
     */
    public function findTags($tags = null): array
    {
        return $this->checkManager->findTags($tags);
    }

    /**
     * @param null|string|string[] $groups
     *
     * @return Group[]
     */
    public function findGroups($groups = null): array
    {
        return $this->checkManager->findGroups($groups);
    }
}
