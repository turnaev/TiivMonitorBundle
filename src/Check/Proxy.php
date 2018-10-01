<?php

/*
 * This file is part of the Sonata Project package.
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * For the full copyright and license information, please view the LICENSE
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

    /**
     * @param \Closure $producer
     */
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
