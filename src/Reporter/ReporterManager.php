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

    /**
     * @param ?string            $alias
     * @param ReporterInterface $reporter
     * @param ?string       $name
     */
    public function addReporter(string $name, ReporterInterface $reporter, string $scope = null)
    {
        if ($scope) {
            $this->reporters[$scope][$name] = $reporter;
        }

        $this->reporters['all'][$name] = $reporter;
    }

    /**
     * @param string $name
     * @param ?string $scope
     *
     * @return ?ReporterInterface
     */
    public function getReporter(string $name, string $scope = null)
    {
        if ($scope) {
            return isset($this->reporters[$scope][$name]) ? $this->reporters[$scope][$name] : null;
        }

        return isset($this->reporters['all'][$name]) ? $this->reporters['all'][$name] : null;
    }

    /**
     * @param ?string $scope
     *
     * @return string[]
     */
    public function getReporterAliases(string $scope = null): array
    {
        if ($scope && isset($this->reporters[$scope])) {
            return array_keys($this->reporters[$scope]);
        }

        return array_keys($this->reporters['all']);
    }
}
