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
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait TraitCommon
{
    protected function getFilterParam(Request $request, $name)
    {
        $v = $request->get($name, []);
        if (\is_scalar($v)) {
            $v = $v ? [$v] : [];
        }

        return !\is_array($v) ? [$v] : $v;
    }

    /**
     * return array [$ids, $checks, $groups, $tags]
     */
    protected function getFilterParams(Request $request): array
    {
        $ids = $this->getFilterParam($request, 'id');
        $checks = $this->getFilterParam($request, 'check');
        $groups = $this->getFilterParam($request, 'group');
        $tags = $this->getFilterParam($request, 'tag');

        return [$ids, $checks, $groups, $tags];
    }
}
