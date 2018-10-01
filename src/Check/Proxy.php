<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Proxy
{
    /**
     * @var \Closure
     */
    protected $producer;

    public function __construct(\Closure $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @return CheckInterface
     */
    public function __invoke()
    {
        return ($this->producer)();
    }
}
