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
        $this->container->addCompilerPass(new AddChecksCompilerPass());
        parent::compile();
    }

    /**
     * @dataProvider checkProvider
     */
    public function testChecksLoaded($checkConf, $checkName, $checkClass, $config)
    {
        $this->load(['checks' => [$checkConf => $config]]);
        $this->compile();

        $registry = $this->container->get('tvi_monitor.checks.registry');

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
                ['check' => ['expectedVersion' => '5.3.3', 'operator' => '=']],
            ],
            'php_version(s)'   => [
                'php_version(s)',
                ['php_version.a', 'php_version.b'],
                Check\PhpVersion\Check::class,
                [
                    'items' => [
                        'a' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                        'b' => ['check' => ['expectedVersion' => '5.3.3', 'operator' => '>']],
                    ]
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
