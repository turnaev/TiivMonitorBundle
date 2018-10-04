<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Exception;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class HttpException extends \Symfony\Component\HttpKernel\Exception\HttpException
{
    public function toArray()
    {
        return ['Error' => ['code' => $this->getCode(), 'message' => $this->getMessage()]];
    }
}
