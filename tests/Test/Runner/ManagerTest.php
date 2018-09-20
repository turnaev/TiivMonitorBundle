<?php

namespace Tvi\MonitorBundle\Runner;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Tvi\MonitorBundle\DependencyInjection\Compiler\AddChecksCompilerPass;
use Tvi\MonitorBundle\DependencyInjection\TviMonitorExtension;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class ManagerTest extends AbstractExtensionTestCase
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

    public function testGetRunner()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $runner = $manager->getRunner();

        $this->assertInstanceOf(Runner::class, $runner);
    }
}
