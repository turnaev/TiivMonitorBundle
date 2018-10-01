<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Check;

use Symfony\Component\Yaml\Yaml;
use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;
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

        $this->load($conf['tvi_monitor']);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        foreach ($manager as $check) {
            $this->assertInstanceOf(CheckInterface::class, $check);
            $this->assertInstanceOf(ResultInterface::class, $check->check());
        }
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    protected function parceYaml($fileName)
    {
        return Yaml::parse(file_get_contents($fileName));
    }
}
