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
 */
class ManagerTest extends ExtensionTestCase
{
    public function testDefaultNoChecks()
    {
        $this->load();
        $this->compile();

        $manager = $this->container->get('tvi_monitor.checks.manager');

        $this->assertCount(0, $manager);
    }

    public function testChecks()
    {
        $conf = [
            'tags' => ['tag1', 'tag2', 'empty'],
            'checks' => [
                'php_version' => [
                    'check' => ['expectedVersion' => '5.3.3', 'operator' => '='],
                    'label' => 'test_label',
                    'tags' => ['tag1', 'tag2'],
                    'group' => 'test',
                ],
                'php_version(s)' => [
                    'items' => [
                        'a' => [
                            'check' => ['expectedVersion' => '5.3.3', 'operator' => '='],
                            'label' => 'test_label',
                            'tags' => ['tag1', 'tag2'],
                        ],
                        'b' => [
                            'check' => ['expectedVersion' => '5.3.3', 'operator' => '='],
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
        $this->assertCount(1, $manager->findGroups('php'));

        $this->assertCount(4, $manager->findTags());
        $this->assertCount(1, $manager->findTags('tag1'));
    }
}
