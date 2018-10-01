<?php

/*
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\redis\Redis;

use Tvi\MonitorBundle\Check\CheckInterface;

use Tvi\MonitorBundle\Check\CheckTrait;
use ZendDiagnostics\Result\Failure;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\Redis implements CheckInterface
{
    use CheckTrait;

    /**
     * Perform the check.
     *
     * @see \ZendDiagnostics\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            return parent::check();
        } catch (\Exception $e) {
            return new Failure($e->getMessage());
        }
    }
}
