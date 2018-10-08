<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\DirWritable;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\DirWritable;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string|array|\Traversable $path Path name or an array of paths
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($path)
    {
        $this->checker = new DirWritable($path);
    }
}
