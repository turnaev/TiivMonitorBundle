<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Reporter;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Output\ConsoleOutput;
use Tvi\MonitorBundle\Reporter\Nagius;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @internal
 */
class ReporterNagiusTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        $this->client = $this->getClient(false);
    }

    public function test_naguis_reporte()
    {
        $runnerManager = $this->client->getContainer()->get('tvi_monitor.runner.manager');
        $runner = $runnerManager->getRunner();

        $outputMock = $this->createMock(ConsoleOutput::class);
        $rs = [];
        $outputMock->method('writeln')->willReturnCallback(static function ($r) use (&$rs) {
            $rs[] = $r;
        });

        $reporter = new Nagius($outputMock);
        $runner->addReporter($reporter);
        $runner->run();

        $rs = implode("\n", $rs);

        $exp = <<<'TXT'
OK      	core:php_version         	Check core:php_version
OK      	core:php_version.a       	Check core:php_version.a
FAIL    	core:php_version.b       	Check core:php_version.b
OK      	test:success:check       	Check test:success:check
OK      	test:success:check.a     	Check test:success:check.a
OK      	test:success:check.b     	Check test:success:check.b
SKIP    	test:skip:check          	Check test:skip:check
SKIP    	test:skip:check.a        	Check test:skip:check.a
SKIP    	test:skip:check.b        	Check test:skip:check.b
WARNING 	test:warning:check       	Check test:warning:check
WARNING 	test:warning:check.a     	Check test:warning:check.a
WARNING 	test:warning:check.b     	Check test:warning:check.b
FAIL    	test:failure:check       	Check test:failure:check
FAIL    	test:failure:check.0     	Check test:failure:check.0
FAIL    	test:failure:check.1     	test:failure1
FAIL    	test:failure:check.2     	test:failure2
FAIL    	test:failure:check.3     	test:failure3
TXT;

        $this->assertSame($exp, $rs);
    }
}
