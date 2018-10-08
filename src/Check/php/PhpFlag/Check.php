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

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\PhpFlag;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var PhpFlag
     */
    private $checker;

    /**
     * @param string|array|\traversable $settingName   PHP setting names to check
     * @param bool                      $expectedValue true or false
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($settingName, $expectedValue)
    {
        $this->checker = new PhpFlag($settingName, $expectedValue);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
