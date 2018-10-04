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

use Tvi\MonitorBundle\Test\Base\ExtensionTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class CheckManagerTest extends ExtensionTestCase
{
    public function test_default_no_checks()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertCount(0, $manager);
    }

    public function test_checks()
    {
        $conf = [
            'checks_search_paths' => [__DIR__.'/../Check'],
            'tags' => ['tag1', 'tag2', 'empty'],
            'checks' => [
                'test:success:check' => [
                    'check' => [],
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2'],
                    'group' => 'test',
                ],
                'test:failure:check(s)' => [
                    'items' => [
                        'a' => [
                            'check' => [],
                            'label' => 'test_label',
                            'tags' => ['tag1', 'tag2'],
                        ],
                        'b' => [
                            'check' => [],
                            'tags' => ['tag1', 'tag2'],
                            'group' => 'test',
                        ],
                    ],
                    'label' => 'test_label',
                    'tags' => ['glob_tag1', 'glob_tag2'],
                    'group' => 'glob_test',
                ],
            ],
        ];

        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertCount(3, $manager->toArray());

        $this->assertCount(2, $manager->findGroups());
        $this->assertCount(1, $manager->findGroups('test'));

        $this->assertCount(4, $manager->findTags());
        $this->assertCount(1, $manager->findTags('tag1'));
    }
}
