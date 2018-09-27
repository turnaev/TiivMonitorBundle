<?php
/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\Redis;

use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\SuccessInterface;
use ZendDiagnostics\Result\WarningInterface;
use ZendDiagnostics\Result\SkipInterface;
use ZendDiagnostics\Result\FailureInterface;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\Redis implements CheckInterface
{
    use CheckTrait;
}
