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

            'tags' => [
                'tag1' => ['name' => 'test tag1', 'descr' => 'descr1'],
                'tag2' => ['descr' => 'descr2'],
                'empty' => null,
            ],
            'groups' => [
                'success' => ['name' => 'test success', 'descr' => 'descr1'],
                'failure' => ['descr' => 'descr2'],
                'empty' => null,
            ],
            'checks' => [
                'test:success:check' => [
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2', 'a'],
                ],
                'test:success:check(s)' => [
                    'items' => [
                        'a' => [
                            'label' => 'test_label',
                            'tags' => ['b'],
                        ],
                        'b' => [
                            'tags' => ['c'],
                        ],
                    ],
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2'],
                    'group' => 'success',
                ],
                'test:failure:check' => [
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2', 'a'],
                ],
                'test:failure:check(s)' => [
                    'items' => [
                        'a' => [
                            'label' => 'test_label',
                            'tags' => ['b'],
                        ],
                        'b' => [
                            'tags' => ['c'],
                        ],
                    ],
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2'],
                    'group' => 'failure',
                ],
            ],
        ];



        $this->load($conf);
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertCount(6, $manager->toArray());

        $this->assertCount(7, $manager->findTags());
        $this->assertCount(1, $manager->findTags('tag1'));
        $this->assertCount(2, $manager->findTags(['tag1', 'c']));
        $this->assertCount(0, $manager->findTags('not_exist'));
        $this->assertCount(1, $manager->findTags('empty'));

        $this->assertCount(5, $manager->findGroups());
        $this->assertCount(1, $manager->findGroups('test'));
        $this->assertCount(2, $manager->findGroups(['test', 'failure']));
        $this->assertCount(0, $manager->findGroups('not_exist'));
        $this->assertCount(1, $manager->findGroups('empty'));
    }
}
