<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\DiskFree;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\DiskFree;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var DiskFree
     */
    private $checker;

    /**
     * @param int|string $size minimum disk size in bytes or a valid byte string (IEC, SI or Jedec)
     * @param string     $path The disk path to check, i.e. '/tmp' or 'C:' (defaults to /)
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($size, $path = '/')
    {
        $this->checker = new DiskFree($size, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
