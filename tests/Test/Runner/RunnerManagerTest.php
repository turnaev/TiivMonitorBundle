<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Runner;

use Tvi\MonitorBundle\Runner\Runner;
use Tvi\MonitorBundle\Runner\RunnerManager;
use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class RunnerManagerTest extends ExtensionTestCase
{
    public function test_get()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $this->assertInstanceOf(RunnerManager::class, $manager);
    }

    public function test_get_runner()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $runner = $manager->getRunner();

        $this->assertInstanceOf(Runner::class, $runner);
    }

    public function test_manager()
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check'],
            'checks' => [
                'test:success:check' => ['check' => [], 'tags' => ['a', 'b'], 'group' => 'test1'],
                'test:success:check(s)' => [
                    'items' => [
                        'a' => [
                            'tags' => ['a', 'c'], 'group' => 'test1',
                            'check' => [],
                        ],
                        'b' => [
                            'tags' => ['a1', 'c1'], 'group' => 'test2',
                            'check' => [],
                        ],
                    ],
                ],
            ],
        ];

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.runner.manager');

        $this->assertCount(1, $manager->findTags('a'));
        $this->assertCount(1, $manager->findTags(['a']));
        $this->assertCount(3, $manager->findTags(['a', 'b', 'c', 'g']));

        $this->assertCount(1, $manager->findGroups('test1'));
        $this->assertCount(1, $manager->findGroups(['test1']));
        $this->assertCount(2, $manager->findGroups(['test1', 'test2']));

        $this->assertCount(2, $manager->findChecks(null, ['test1', 'test2'], ['a', 'b']));
    }
}
