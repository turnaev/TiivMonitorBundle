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

use PHPUnit\Framework\TestCase;
use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Check\php\PhpVersion\Check;
use Tvi\MonitorBundle\Check\Proxy;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 * @coversNothing
 */
class CheckArraybleTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('testGroup');

        $check1 = new Check('7.0', '=');
        $check1->setId('php_version');

        $this->group->addCheck($check1->getId(), $check1);

        $check2 = new Proxy(static function () {
            $check2 = new Check('7.0', '=');
            $check2->setId('php_version.proxy');

            return $check2;
        });

        $this->group->addCheck('php_version.proxy', $check2);
    }

    public function test_count()
    {
        $this->assertCount(2, $this->group);
    }

    public function test_offset_exists()
    {
        $this->assertTrue(isset($this->group['php_version']));
        $this->assertFalse(isset($this->group['php_version_not']));
    }

    public function test_offset_get()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->group['php_version']);
        $this->assertInstanceOf(CheckInterface::class, $this->group['php_version.proxy']);
    }

    public function test_offset_set()
    {
        $this->assertCount(2, $this->group);

        $check = new Check('7.0', '=');
        $check->setId('php_version.w');

        $this->group['php_version.w'] = $check;

        $this->assertCount(3, $this->group);
    }

    public function test_offset_unset()
    {
        $this->assertCount(2, $this->group);
        unset($this->group['php_version']);
        $this->assertCount(1, $this->group);
    }

    public function test_to_array()
    {
        $this->assertCount(2, $this->group->toArray());
    }

    public function test_traversable()
    {
        foreach ($this->group as $check) {
            $this->assertInstanceOf(CheckInterface::class, $check);
        }
    }
}
