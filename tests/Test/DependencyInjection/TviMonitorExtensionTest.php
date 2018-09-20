<?php

namespace Tvi\MonitorBundle\DependencyInjection;

use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorExtensionTest extends ExtensionTestCase
{
    protected function compile()
    {
        parent::compile();

        $doctrineMock = $this->getMockBuilder('Doctrine\Common\Persistence\ConnectionRegistry')->getMock();
        $this->container->set('doctrine', $doctrineMock);
    }

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
        $tags = $registry->getTags();

        $this->assertCount(0, $tags);
    }

    public function testMailer()
    {
        $this->load();

        $this->assertEquals(false, $this->container->has('tvi_monitor.reporter.swift_mailer'));

        $this->load([
            'reporters' => [
                'mailer' => [
                    'recipient'       => 'foo@example.com',
                    'sender'          => 'bar@example.com',
                    'subject'         => 'Health Check',
                    'send_on_warning' => true,
                ],
            ],
        ]);

        $this->assertContainerBuilderHasService('tvi_monitor.reporter.swift_mailer');
    }

    /**
     * @dataProvider mailerConfigProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidMailerConfig($config)
    {
        $this->load($config);
    }

    public function mailerConfigProvider()
    {
        return [
            "only_recipient"  => [
                'reporters' => [
                    'mailer' => [
                        'recipient' => 'foo@example.com',
                    ],
                ],
            ],
            "without_subject" => [
                'reporters' => [
                    'mailer' => [
                        'recipient' => 'foo@example.com',
                        'sender'    => 'bar@example.com',
                        'subject'   => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider invalidCheckProvider
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidExpressionConfig(array $config)
    {
        $this->load(['checks' => ['expressions' => $config]]);
        $this->compile();
    }

    public function invalidCheckProvider()
    {
        /* @todo */
        return [
            "one" => [['foo']],
            "two" => [['foo' => ['critical_expression' => 'true']]],
            "three" => [['foo' => ['label' => 'foo']]],
        ];
    }

    /**
     * @dataProvider checkProvider
     */
    public function testChecksLoaded($checkName, $checkClass, $config)
    {
        $this->load(['checks' => $config]);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        if (is_array($checkName)) {
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
            'tvi_php_version'    => [
                'tvi_php_version',
                Check\PhpVersion\Check::class,
                [
                    'tvi_php_version' => [
                        'check' => ['expectedVersion' => '5.3.3', 'operator' => '='],
                        'tags'  => ['test'],
                    ],
                ],
            ],
            'tvi_php_version(s)' => [
                ['tvi_php_version.a', 'tvi_php_version.b'],
                Check\PhpVersion\Check::class,
                [
                    'tvi_php_version(s)' => [
                        'items' => [
                            'a' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                            'b' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                        ],
                        'tags'  => ['test'],
                    ],
                ],
            ],
        ];
    }
}
