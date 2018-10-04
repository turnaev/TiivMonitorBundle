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

use ZendDiagnostics\Result\Warning;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\AbstractCheck implements CheckInterface
{
    use CheckTrait;

    public function check()
    {
        return new Warning('warning', ['status' => 'warning']);
    }
}
