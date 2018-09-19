<?php

namespace Tvi\MonitorBundle\Test\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\TviMonitorExtension;
use Tvi\MonitorBundle\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TviMonitorExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [new TviMonitorExtension()];
    }

    protected function compile()
    {
        $doctrineMock = $this->getMockBuilder('Doctrine\Common\Persistence\ConnectionRegistry')->getMock();
        $this->container->set('doctrine', $doctrineMock);

        $this->container->addCompilerPass(new AddChecksCompilerPass());

        parent::compile();
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

        $this->assertCount(2, $tags);
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
    public function testChecksLoaded($checkConf, $checkName, $checkClass, $config)
    {
        $this->load(['checks' => [$checkConf => $config]]);
        $this->compile();

        $registry = $this->container->get('tvi_monitor.checks.manager');

        if (is_array($checkName)) {

            foreach ($checkName as $i) {
                $check = $registry[$i];
                $this->assertInstanceOf($checkClass, $check);
            }
        } else {
            $check = $registry[$checkName];
            $this->assertInstanceOf($checkClass, $check);
        }
    }

    public function checkProvider()
    {
        return [
            'php_version'      => [
                'php_version',
                'php_version',
                Check\PhpVersion\Check::class,
                ['check' => ['expectedVersion' => '5.3.3', 'operator' => '='], 'tags'=>['test']],
            ],
            'php_version(s)'   => [
                'php_version(s)',
                ['php_version.a', 'php_version.b'],
                Check\PhpVersion\Check::class,
                [
                    'items' => [
                        'a' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                        'b' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                    ],
                    'tags'=>['test']
                ],
            ],
            'php_extension'    => [
                'php_extension',
                'php_extension',
                Check\PhpExtension\Check::class,
                ['check' => ['extensionName' => ['xdebug']], 'tags' => ['tag']],
            ],
            'php_extension(s)' => [
                'php_extension(s)',
                ['php_extension.a', 'php_extension.b'],
                Check\PhpExtension\Check::class,
                [
                    'items' => [
                        'a' => ['check' => ['extensionName' => ['xdebug']]],
                        'b' => ['check' => ['extensionName' => 'xdebug']],
                    ]
                ],
            ],
        ];
    }
}
