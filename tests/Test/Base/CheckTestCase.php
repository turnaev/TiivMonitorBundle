<?php

namespace Tvi\MonitorBundle\Test\Base;

use Tvi\MonitorBundle\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckTestCase extends ExtensionTestCase
{
    /**
     * @param string $confPath
     *
     * @throws \Exception
     */
    protected function iterateConfTest(string $confPath)
    {
        $conf = $this->parceYaml($confPath);

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        foreach ($manager as $check) {
            $this->assertInstanceOf(CheckInterface::class, $check);
            $this->assertInstanceOf(ResultInterface::class, $check->check());
        }
    }
}
