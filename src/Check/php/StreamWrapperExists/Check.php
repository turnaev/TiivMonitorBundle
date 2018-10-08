<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\StreamWrapperExists;

use ZendDiagnostics\Check\StreamWrapperExists;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string|array|\Traversable $wrappers Stream wrapper name or an array of names
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($wrappers)
    {
        $this->checker = new StreamWrapperExists($wrappers);
    }
}
