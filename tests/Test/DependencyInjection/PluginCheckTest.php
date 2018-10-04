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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;
use Tvi\MonitorBundle\Test\Check\TestSuccessCheck\Check as TestSuccessCheck;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class PluginCheckTest extends ExtensionTestCase
{
    public function test_plugin_check()
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check'],
            'checks' => [
                'test:success:check' => ['check' => []],
                'test:success:check(s)' => [
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

        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check']);
        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check.a']);
        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check.b']);
    }

    public function test_plugin_check_ordered()
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check'],
            'checks' => [
                'test:success:check' => ['check' => []],
                'test:success:check(s)' => [
                    'items' => [
                        [
                            'check' => [],
                        ],
                        [
                            'check' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check']);
        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check.0']);
        $this->assertInstanceOf(TestSuccessCheck::class, $manager['test:success:check.1']);
    }

    public function test_bad_path_plugin_check()
    {
        $this->expectException(InvalidConfigurationException::class);

        $conf = [
            'checks' => [
                'test:success:check' => ['check' => []],
                'test:success:check(s)' => [
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
