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

use Tvi\MonitorBundle\Check;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class PluginCheckTest extends ExtensionTestCase
{
    public function testPlugCheck()
    {
        $conf = [
            'checks_search_paths' => [__DIR__ . '/../Check/TestCheck/'],
            'checks'              => [
                'test_check'    => ['check' => []],
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

    /**
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testBasdPlugCheck()
    {
        $conf = [
            'checks'              => [
                'test_check'    => ['check' => []],
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
