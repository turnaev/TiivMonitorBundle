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
class TviMonitorExtensionTest extends ExtensionTestCase
{
    public function testDefaultNoChecks()
    {
        $this->load();
        $this->compile();

        $this->assertCount(0, $this->container->get('tvi_monitor.checks.manager'));
    }

    public function testTags()
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

    public function testMailer()
    {
        $this->load();

        $this->assertFalse($this->container->has('tvi_monitor.reporter.swift_mailer'));

        $this->load([
            'reporters' => [
                'mailer' => [
                    'recipient' => 'foo@example.com',
                    'sender' => 'bar@example.com',
                    'subject' => 'Health Check',
                    'send_on_warning' => true,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasService('tvi_monitor.reporter.swift_mailer');
    }

    /**
     * @dataProvider mailerConfigProvider
     *
     * @param mixed $config
     */
    public function testInvalidMailerConfig($config)
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
    public function testInvalidExpressionConfig(array $config)
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
     *
     * @param mixed $checkName
     * @param mixed $checkClass
     * @param mixed $config
     */
    public function testChecksLoaded($checkName, $checkClass, $config)
    {
        $this->load(['checks' => $config]);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        if (\is_array($checkName)) {
            foreach ($checkName as $i) {
                $check = $manager[$i];
                $this->assertInstanceOf($checkClass, $check);
            }
        } else {
            $check = $manager[$checkName];
            $this->assertInstanceOf($checkClass, $check);
        }
    }

    public function checkProvider()
    {
        return [
            'php_version' => [
                'php_version',
                Check\php\PhpVersion\Check::class,
                [
                    'php_version' => [
                        'check' => ['expectedVersion' => '5.3.3', 'operator' => '='],
                        'tags' => ['test'],
                    ],
                ],
            ],
            'php_version(s)' => [
                ['php_version.a', 'php_version.b'],
                Check\php\PhpVersion\Check::class,
                [
                    'php_version(s)' => [
                        'items' => [
                            'a' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                            'b' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                        ],
                        'tags' => ['test'],
                    ],
                ],
            ],
        ];
    }
}
