<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ExtensionLoaded;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\ExtensionLoaded;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var ExtensionLoaded
     */
    private $checker;

    /**
     * @param string|array|Traversable $extensionName PHP extension name or an array of names
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($extensionName)
    {
        $this->checker = new ExtensionLoaded($extensionName);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
