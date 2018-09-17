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
//                'php_extension(s)' => ['items'=>['sss'=>['extensionName'=>['xdebug', 'test']]]],
//                'php_version(s)' => ['items'=>['a'=>['expectedVersion'=>'5.3.3', 'operator'=> '>'], 'b'=>['expectedVersion'=>'5.3.3', 'operator' => '<']], 'tags'=>['tag', 'b', 'c'], 'label'=>'sss22s'],
                'php_version'    => ['expectedVersion'=>'5.3.3', 'operator'=> '=', 'tags'=>['tag'], 'group'=>'php', 'label'=>'ssss'],
                'php_extension' => ['extensionName'=>['xdebug'], 'tags'=>['tag']],
            ],
        ];

        $this->load($conf);

        $this->compile();


        $runnerManager = $this->container->get('tvi_monitor.checks.registry');
        v($runnerManager);

        //v();
//        try {
//            $check = $this->container->get('monitor.check.php_version');
//            v($check);
//            //v($check->check());
//
//            v($check);
//
//            $check = $this->container->get('monitor.check.php_version_collection');
//            v($check);
//            //v($check->check());
//        } catch (\Error $e) {
//
//            v($e);
//        }



        exit;
        //$this->assertTrue($this->container->hasParameter('liip_monitor.default_group'));
        //$this->assertSame('foo_bar', $this->container->getParameter('liip_monitor.default_group'));
    }
}
