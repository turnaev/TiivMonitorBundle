<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\fs\JsonFile;

use JMS\Serializer\Annotation as JMS;
use ZendDiagnostics\Check\JsonFile;
use Tvi\MonitorBundle\Check\CheckAbstract;

/**
 * @JMS\ExclusionPolicy("all")
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends CheckAbstract
{
    /**
     * @var JsonFile
     */
    private $checker;

    /**
     * @param string|array|\Traversable $files Path name or an array / Traversable of paths
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($files)
    {
        $this->checker = new JsonFile($files);
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        return $this->checker->check();
    }
}
