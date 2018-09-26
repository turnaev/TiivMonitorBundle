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
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Check\PhpVersion\Check;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('testGroup');

        $check = new Check('7.0', '=');
        $check->setId('php_version');
        $this->group->addCheck($check->getId(), $check);
    }

    public function testGetName()
    {
        $this->assertEquals('testGroup', $this->group->getName());
    }

    public function testGetChecknames()
    {
        $this->assertCount(1, $this->group->getChecknames());
    }

    public function testAddCheck()
    {
        $check = new Check('7.0', '=');
        $check->setId('php_version.b');
        $this->group->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->group->getChecknames());
    }

    public function testGetLabel()
    {
        $this->assertEquals('testGroup (1)', $this->group->getLabel());
    }
}
