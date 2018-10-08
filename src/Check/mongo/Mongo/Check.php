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

use ZendDiagnostics\Check\Mongo;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string $connectionUri
     */
    public function __construct($connectionUri = 'mongodb://127.0.0.1/')
    {
        $this->checker = new Mongo($connectionUri);
    }
}
