<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\DependencyInjection;

use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class PluginCheckTest extends ExtensionTestCase
{
    public function test_plug_check()
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check/TestCheck/'],
            'checks' => [
                'test_check' => ['check' => []],
                'test_check(s)' => [
                    'items' => [
                        'a' => [
                            'check' => [],
                        ],
                        'b' => [
                            'check' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertInstanceOf(\Tvi\MonitorBundle\Test\Check\TestCheck\Check::class, $manager['test_check']);
        $this->assertInstanceOf(\Tvi\MonitorBundle\Test\Check\TestCheck\Check::class, $manager['test_check.a']);
        $this->assertInstanceOf(\Tvi\MonitorBundle\Test\Check\TestCheck\Check::class, $manager['test_check.b']);
    }

    public function test_basd_plug_check()
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $conf = [
            'checks' => [
                'test_check' => ['check' => []],
                'test_check(s)' => [
                    'items' => [
                        'a' => [
                            'check' => [],
                        ],
                        'b' => [
                            'check' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->load($conf);
        $this->compile();
    }
}