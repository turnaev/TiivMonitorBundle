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
class CheckPluginFinderPath
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string[]
     */
    protected $paths = [];

    /**
     * @param string|string[] $path
     */
    public function __construct(string $basePath, $path)
    {
        $basePath = \rtrim($basePath, \DIRECTORY_SEPARATOR);
        $this->basePath = $basePath;
        $this->paths = \is_string($path) ? [$path] : $path;
    }

    public function getPathes()
    {
        return array_map(
            function ($i) {
                return sprintf('%s%s%s', $this->basePath, \DIRECTORY_SEPARATOR, $i);
            },
            $this->paths);
    }
}
