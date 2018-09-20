<?php

namespace Tvi\MonitorBundle\Check;

use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('testGroup');

        $check = new PhpVersion\Check('7.0', '=');
        $check->setId('tvi_php_version');
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
        $check = new PhpVersion\Check('7.0', '=');
        $check->setId('tvi_php_version.b');
        $this->group->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->group->getChecknames());
    }

    public function testGetLabel()
    {
        $this->assertEquals('testGroup (1)', $this->group->getLabel());
    }
}
