<?php

namespace Tvi\MonitorBundle\Check;

use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    /**
     * @var Tag
     */
    protected $tag;

    protected function setUp()
    {
        $this->tag = new Tag('testTag');

        $check = new PhpVersion\Check('7.0', '=');
        $check->setId('php_version');
        $this->tag->addCheck($check->getId(), $check);
    }

    public function testGetName()
    {
        $this->assertEquals('testTag', $this->tag->getName());
    }

    public function testGetChecknames()
    {
        $this->assertCount(1, $this->tag->getChecknames());
    }

    public function testAddCheck()
    {
        $check = new PhpVersion\Check('7.0', '=');
        $check->setId('php_version.b');
        $this->tag->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->tag->getChecknames());
    }

    public function testGetLabel()
    {
        $this->assertEquals('testTag (1)', $this->tag->getLabel());
    }
}
