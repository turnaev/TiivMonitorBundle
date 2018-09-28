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

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class CheckTest extends TestCase
{
    /**
     * @var Check
     */
    protected $check;

    protected function setUp()
    {
        $this->check = new Check('7.0', '=');
        $this->check->setId('php_version');

        $this->check->setTags(['tag1', 'tag2']);
        $this->check->setGroup('group');
    }

    public function testId()
    {
        $this->assertEquals('php_version', $this->check->getId());
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
