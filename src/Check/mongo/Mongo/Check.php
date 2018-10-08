<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\mongo\Mongo;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\Mongo;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var Mongo
     */
    private $checker;

    /**
     * @param string $connectionUri
     */
    public function __construct($connectionUri = 'mongodb://127.0.0.1/')
    {
        $this->checker = new Mongo($connectionUri);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
