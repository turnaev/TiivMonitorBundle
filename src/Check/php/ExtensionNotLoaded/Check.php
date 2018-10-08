<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ExtensionNotLoaded;

use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Check\ExtensionLoaded;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param int|string $size minimum disk size in bytes or a valid byte string (IEC, SI or Jedec)
     * @param string     $path The disk path to check, i.e. '/tmp' or 'C:' (defaults to /)
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($size, $path = '/')
    {
        $this->checker = new ExtensionLoaded($size, $path);
    }

    /**
     * @return Failure|Success|ResultInterface
     */
    public function check()
    {
        $r = $this->checker->check();
        if ($r instanceof Success) {
            return new Failure($r->getMessage(), $r->getData());
        }

        return new Success($r->getMessage(), $r->getData());
    }
}
