<?php

namespace Tvi\MonitorBundle\Check\PhpVersion;

use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Test extends ExtensionTestCase
{
    public function testCheck()
    {
        $conf = $this->parceYaml(__DIR__.'/config.example.yml');
        $conf = $conf['tvi_monitor'];

//        $conf = []
//
//        $this->load($conf);
//        $this->compile();
//
////        $manager = $this->container->get('tvi_monitor.checks.manager');
//
////        foreach ($manager as $check) {
////            $this->assertInstanceOf(Check::class, $check);
////        }
//
//        exit;
    }

}
