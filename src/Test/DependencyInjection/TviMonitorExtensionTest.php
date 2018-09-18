<?php

namespace Tvi\MonitorBundle\Test\DependencyInjection;

use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddGroupsCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\Compiler\CheckCollectionTagCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\Compiler\CheckTagCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\Compiler\GroupRunnersCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\TviMonitorExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

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
        //$doctrineMock = $this->getMockBuilder('Doctrine\Common\Persistence\ConnectionRegistry')->getMock();
        //$this->container->set('doctrine', $doctrineMock);
        //$this->container->addCompilerPass(new AddGroupsCompilerPass());
        //$this->container->addCompilerPass(new GroupRunnersCompilerPass());
        //$this->container->addCompilerPass(new CheckTagCompilerPass());
       //$this->container->addCompilerPass(new CheckCollectionTagCompilerPass());

        $this->container->addCompilerPass(new AddChecksCompilerPass());
        parent::compile();
    }

    public function testConfig()
    {
        $conf = [
            //'reporters'=>['mailer'=>['recipient'=>'ss', 'sender'=>'dd', 'subject'=>'test']],
            //'tags'=>['tag'=>['title'=>'TAG'], 'tag2'=>[]],
            'tags'=>['tag'=>['title'=>'TAG']],
            'checks_search_paths' => [],
            'checks' => [
                /////////////////////////
                'php_extension'    => [
                    'check' => ['extensionName' => ['xdebug']],
                    'tags'  => ['tag'],
                ],
                /////////////////////////
                'php_extension(s)' => [
                    'items' => [
                        'a1' => [
                            'check' => [
                                'extensionName' => ['xdebug', 'test']
                            ]
                        ],
                    ],
                ],
                /////////////////////////
                'php_version'      => [
                    'check' => [
                        'expectedVersion' => '5.3.3',
                        'operator'        => '=',
                    ],
                    'tags'  => ['tag'],
                    'tags'  => null,
                    'group' => 'php',
                    'label' => 'ssss',
                ],
                /////////////////////////
                'php_version(s)'   => [
                    'items' => [
                        'a2' => [
                            'check' => [
                                'expectedVersion' => '5.3.3',
                                'operator'        => '>',
                            ],
                            'tags'  => ['tag2'],
                            'group'=>'test'
                        ],
                        'b2' => [
                            'check' => [
                                'expectedVersion' => '5.3.3',
                                'operator'        => '<',
                            ],
                        ],
                    ],
                    'tags'  => ['tag1'],
                ],
            ],
        ];


        $this->load($conf);
        $this->compile();

        //$registry = $this->container->get('tvi_monitor.checks.registry');
        //v($registry);

        exit;
    }
}
