<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\PhpFlag;

use ZendDiagnostics\Check\PhpFlag;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string|array|traversable $settingName   PHP setting names to check
     * @param bool                     $expectedValue true or false
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($settingName, $expectedValue)
    {
        $this->checker = new PhpFlag($settingName, $expectedValue);
    }
}
