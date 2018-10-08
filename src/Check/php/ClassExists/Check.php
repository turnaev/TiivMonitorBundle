<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\ClassExists;

use ZendDiagnostics\Check\ClassExists;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @param string|array|\Traversable $classNames Class name or an array of classes
     * @param bool                      $autoload   Use autoloader when looking for classes? (defaults to true)
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($classNames, $autoload = true)
    {
        $this->checker = new ClassExists($classNames, $autoload);
    }
}
