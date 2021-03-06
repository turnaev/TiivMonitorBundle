<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\Expression;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Tvi\MonitorBundle\Check\CheckPluginAbstract;
use Tvi\MonitorBundle\Exception\FeatureRequired;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Plugin extends CheckPluginAbstract
{
    const DESCR =
<<<'TXT'
expression description
TXT;

    const PATH = __DIR__;

    const GROUP = 'php';
    const CHECK_NAME = 'core:expression';

    /**
     * @throws FeatureRequired
     */
    public function checkRequirements(array $checkSettings)
    {
        if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
            throw new FeatureRequired('The symfony/expression-language is required for '.static::class.' check.');
        }
    }

    protected function _check(ArrayNodeDefinition $node): ArrayNodeDefinition
    {
        $node = $node
            ->children()
                ->arrayNode('check')
                    ->addDefaultsIfNotSet()
                    ->validate()
                        ->ifTrue(static function ($value) {
                            return !$value['warningExpression'] && !$value['criticalExpression'];
                        })
                        ->thenInvalid('A warningExpression or a criticalExpression must be set.')
                    ->end()
                    ->children()
                      ->scalarNode('criticalExpression')->defaultNull()->example('ini(\'apc.stat\') == 0')->end()
                      ->scalarNode('criticalMessage')->defaultNull()->end()
                      ->scalarNode('warningExpression')->defaultNull()->example('ini(\'short_open_tag\') == 1')->end()
                      ->scalarNode('warningMessage')->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        $this->_addition($node);

        return $node;
    }
}
