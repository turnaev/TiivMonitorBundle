<?php

namespace Tvi\MonitorBundle\Check;

use PHPUnit\Framework\TestCase;
use Tvi\MonitorBundle\Check\PhpVersion\Check;

class CheckTest extends TestCase
{
    /**
     * @var Check
     */
    protected $check;

    protected function setUp()
    {
        $this->check = new PhpVersion\Check('7.0', '=');
        $this->check->setId('tvi_php_version');

        $this->check->setTags(['tag1', 'tag2']);
        $this->check->setGroup('group');
    }

    public function testId()
    {
        $this->assertEquals('tvi_php_version', $this->check->getId());
    }

    public function testGroup()
    {
        $this->assertEquals('group', $this->check->getGroup());
    }

    public function testTags()
    {
        $this->assertEquals(['tag1', 'tag2'], $this->check->getTags());
    }

    public function testLabel()
    {
        $this->assertEquals('Check', $this->check->getLabel());
    }

    public function testAdditionParams()
    {
        $this->check->setAdditionParams(['id'=>'id', 'label'=>'test', 'tags'=>['tag'], 'group'=>'testGroup']);

        $this->assertEquals('test', $this->check->getLabel());
        $this->assertEquals(['tag'], $this->check->getTags());
        $this->assertEquals('testGroup', $this->check->getGroup());
        $this->assertEquals('id', $this->check->getId());
    }
}
