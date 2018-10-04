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
use Tvi\MonitorBundle\Test\Check\TestSuccessCheck\Check as TestSuccessCheck;
use Tvi\MonitorBundle\Test\Check\TestFailureCheck\Check as TestFailureCheck;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class TviMonitorExtensionTest extends ExtensionTestCase
{
    public function test_default_no_checks()
    {
        $this->load();
        $this->compile();

        $this->assertCount(0, $this->container->get('tvi_monitor.checks.manager'));
    }

    public function test_tags()
    {
        $conf = [
            'tags' => [
                'tag',
                'tag1',
            ],
        ];

        $this->load($conf);
        $this->compile();

        $registry = $this->container->get('tvi_monitor.checks.manager');
        $tags = $registry->findTags();

        $this->assertCount(0, $tags);
    }

    public function test_mailer()
    {
        $this->load();

        $this->assertFalse($this->container->has('tvi_monitor.reporter.swift_mailer'));

        $conf = [
            'reporters' => [
                'mailer' => [
                    'recipient' => 'foo@example.com',
                    'sender' => 'bar@example.com',
                    'subject' => 'Health Check',
                    'send_on_warning' => true,
                ],
            ],
        ];
        $this->load($conf);

        $this->assertContainerBuilderHasService('tvi_monitor.reporter.mailer');
    }

    /**
     * @dataProvider mailerConfigProvider
     */
    public function test_invalid_mailer_config($config)
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->load($config);
    }

    public function mailerConfigProvider()
    {
        return [
            'only_recipient' => [
                'reporters' => [
                    'mailer' => [
                        'recipient' => 'foo@example.com',
                    ],
                ],
            ],
            'without_subject' => [
                'reporters' => [
                    'mailer' => [
                        'recipient' => 'foo@example.com',
                        'sender' => 'bar@example.com',
                        'subject' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidCheckProvider
     */
    public function test_invalid_expression_config(array $config)
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->load(['checks' => ['expressions' => $config]]);
        $this->compile();
    }

    public function invalidCheckProvider()
    {
        /* @todo */
        return [
            'one' => [['foo']],
            'two' => [['foo' => ['critical_expression' => 'true']]],
            'three' => [['foo' => ['label' => 'foo']]],
        ];
    }

    /**
     * @dataProvider checkProvider
     */
    public function test_checks_loaded($checkName, $checkClass, $resultClass, $config)
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check'],
            'checks' => $config,
        ];

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $checkName = \is_string($checkName) ? [$checkName] : $checkName;

        foreach ($checkName as $i) {
            $check = $manager[$i];
            $this->assertInstanceOf($checkClass, $check);
            $res = $check->check();
            $this->assertInstanceOf($resultClass, $res);
        }
    }

    public function checkProvider()
    {
        return [
            'test:success:check' => [
                'test:success:check',
                TestSuccessCheck::class,
                Success::class,
                [
                    'test:success:check' => [
                        'check' => [],
                        'tags' => ['test'],
                    ],
                ],
            ],
            'test:success:check(s)' => [
                ['test:success:check.a', 'test:success:check.b'],
                TestSuccessCheck::class,
                Success::class,
                [
                    'test:success:check(s)' => [
                        'items' => [
                            'a' => ['check' => []],
                            'b' => ['check' => []],
                        ],
                        'tags' => ['test'],
                    ],
                ],
            ],
            'test:failure:check' => [
                'test:failure:check',
                TestFailureCheck::class,
                Failure::class,
                [
                    'test:failure:check' => [
                        'check' => [],
                        'tags' => ['test'],
                    ],
                ],
            ],
            'test:failure:check(s)' => [
                ['test:failure:check.a', 'test:failure:check.b'],
                TestFailureCheck::class,
                Failure::class,
                [
                    'test:failure:check(s)' => [
                        'items' => [
                            'a' => ['check' => []],
                            'b' => ['check' => []],
                        ],
                        'tags' => ['test'],
                    ],
                ],
            ],
        ];
    }
}
