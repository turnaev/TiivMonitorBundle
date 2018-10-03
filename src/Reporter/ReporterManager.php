<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Reporter;

use ZendDiagnostics\Runner\Reporter\ReporterInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class ReporterManager
{
    /**
     * @var ReporterInterface[]
     */
    protected $reporters = [];

    public function addReporter(string $alias, ReporterInterface $reporter, ?string $scope = null)
    {
        if ($scope) {
            $this->reporters[$scope][$alias] = $reporter;
        }

        $this->reporters['all'][$alias] = $reporter;
    }

    /**
     * @return ReporterInterface
     */
    public function getReporter(string $alias, ?string $scope = null): ?ReporterInterface
    {
        if ($scope) {
            return isset($this->reporters[$scope][$alias]) ? $this->reporters[$scope][$alias] : null;
        }

        return isset($this->reporters['all'][$alias]) ? $this->reporters['all'][$alias] : null;
    }

    /**
     * @return string[]
     */
    public function getReporterAliases(?string $scope = null): array
    {
        if ($scope && isset($this->reporters[$scope])) {
            return array_keys($this->reporters[$scope]);
        }

        return array_keys($this->reporters['all']);
    }
}
