<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Serializer;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait ApiCommonTrait
{
    /**
     * @var RunnerManager
     */
    protected $runnerManager;

    /**
     * @var ReporterManager
     */
    protected $reporterManager;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(RunnerManager $runnerManager, ReporterManager $reporterManager, Serializer $serializer)
    {
        $this->runnerManager = $runnerManager;
        $this->reporterManager = $reporterManager;
        $this->serializer = $serializer;
    }

    /**
     * return array [$ids, $checks, $groups, $tags].
     */
    protected function getFilterParams(Request $request): array
    {
        $id = $request->get('id', []);
        if (\is_scalar($id)) {
            $id = $id ? [$id] : [];
        }

        $ids = !\is_array($id) ? [$id] : $id;

        $checks = $request->get('check', []);
        if (\is_scalar($checks)) {
            $checks = $checks ? [$checks] : [];
        }
        $checks = !\is_array($checks) ? [$checks] : $checks;

        $groups = $request->get('group', []);
        if (\is_scalar($groups)) {
            $groups = $groups ? [$groups] : [];
        }
        $groups = !\is_array($groups) ? [$groups] : $groups;

        $tags = $request->get('tag', []);
        if (\is_scalar($tags)) {
            $tags = $tags ? [$tags] : [];
        }
        $tags = !\is_array($tags) ? [$tags] : $tags;

        return [$ids, $checks, $groups, $tags];
    }
}
