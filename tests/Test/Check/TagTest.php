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
use Tvi\MonitorBundle\Check\php\PhpVersion\Check;
use Tvi\MonitorBundle\Check\Tag;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class TagTest extends TestCase
{
    /**
     * @var Tag
     */
    protected $tag;

    protected function setUp()
    {
        $this->tag = new Tag('testTag');

        $check = new Check('7.0', '=');
        $check->setId('php_version');
        $this->tag->addCheck($check->getId(), $check);
    }

    public function testGetName()
    {
        $this->assertSame('testTag', $this->tag->getName());
    }

    public function testGetChecknames()
    {
        $this->assertCount(1, $this->tag->getChecknames());
    }

    public function testAddCheck()
    {
        $check = new Check('7.0', '=');
        $check->setId('php_version.b');
        $this->tag->addCheck($check->getId(), $check);

        $this->assertCount(2, $this->tag->getChecknames());
    }

    public function testGetLabel()
    {
        $this->assertSame('testTag (1)', $this->tag->getLabel());
    }
}
