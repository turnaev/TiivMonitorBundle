<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check\TestWarningCheck;

use Tvi\MonitorBundle\Check\CheckAbstract;
use ZendDiagnostics\Result\Warning;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    public function check()
    {
        return new Warning('warning', ['status' => 'warning']);
    }
}
