<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\YamlFile;

use ZendDiagnostics\Check\YamlFile;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string|array|\Traversable $files Path name or an array / Traversable of paths
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($files)
    {
        $this->checker = new YamlFile($files);
    }
}
