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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
interface CheckPluginInterface
{
    const PATH = null;

    const GROUP = null;
    const DESCR = null;

    const CHECK_NAME = null;

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings);

    public function checkConf(TreeBuilder $builder): ArrayNodeDefinition;

    public function checkFactoryConf(TreeBuilder $builder): ArrayNodeDefinition;
}
