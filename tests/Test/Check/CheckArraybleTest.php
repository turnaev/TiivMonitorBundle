<?php

namespace Tvi\MonitorBundle\Check;

use PHPUnit\Framework\TestCase;

class CheckArraybleTest extends TestCase
{
    /**
     * @var Group
     */
    protected $group;

    protected function setUp()
    {
        $this->group = new Group('testGroup');

        $check1 = new PhpVersion\Check('7.0', '=');
        $check1->setId('tvi_php_version');

        $this->group->addCheck($check1->getId(), $check1);

        $check2 = new Proxy(function () {
            $check2 = new PhpVersion\Check('7.0', '=');
            $check2->setId('tvi_php_version..proxy');

            return $check2;
        });

        $this->group->addCheck('tvi_php_version.proxy', $check2);
    }

    public function testCount()
    {
        $this->assertCount(2, $this->group);
    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->group['tvi_php_version']));
        $this->assertFalse(isset($this->group['tvi_php_version_not']));
    }

    public function testOffsetGet()
    {
        $this->assertInstanceOf(CheckInterface::class, $this->group['tvi_php_version']);
        $this->assertInstanceOf(CheckInterface::class, $this->group['tvi_php_version.proxy']);
    }

    public function testOffsetSet()
    {
        $this->assertCount(2, $this->group);

        $check = new PhpVersion\Check('7.0', '=');
        $check->setId('tvi_php_version.w');

        $this->group['tvi_php_version.w'] = $check;

        $this->assertCount(3, $this->group);
    }

    public function testOffsetUnset()
    {
        $this->assertCount(2, $this->group);
        unset($this->group['tvi_php_version']);
        $this->assertCount(1, $this->group);
    }

    public function testToArray()
    {
        $this->assertCount(2, $this->group->toArray());
    }

    public function testTraversable()
    {
        foreach ($this->group as $check) {
            $this->assertInstanceOf(CheckInterface::class, $check);
        }
    }
}
