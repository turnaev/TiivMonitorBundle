<?php
/**
 * This file is part of the `monitor-bundle` project.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\DependencyInjection;

use Symfony\Bundle\FrameworkBundle\Client;
use Tvi\MonitorBundle\Check\Group;
use Tvi\MonitorBundle\Check\Tag;
use Tvi\MonitorBundle\Test\Base\WebTestCase;
use Tvi\MonitorBundle\Test\Check\TestSuccessCheck\Check;


/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class SerializationTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    private $serializer;

    protected function setUp()
    {
        $this->client = $this->getClient(false);
        $this->serializer = $this->client->getContainer()->get('jms_serializer');
    }

    public function test_tags()
    {
        $tags = [];

        $tag = new Tag('test');
        $tags[] = $tag;

        $check1 = new Check();
        $check1->setId('test:success:check.a');
        $tag->addCheck($check1->getId(), $check1);

        $tag = new Tag('test2', 'test2', 'test2');
        $tags[] = $tag;

        $tag->addCheck($check1->getId(), $check1);
        $check2 = new Check();
        $check2->setId('test:success:check.b');
        $tag->addCheck($check2->getId(), $check2);

        $json = $this->serializer->serialize($tags, 'json');

        $this->assertJson($json);

        $expJson = <<<'JSON'
[
{"id":"test","name":"test","label":"test(1)","count":1,"checks":["test:success:check.a"]},
{"id":"test2","name":"test2","label":"test2(2)","descr":"test2","count":2,"checks":["test:success:check.a","test:success:check.b"]}
]
JSON;

        $this->assertJsonStringEqualsJsonString($expJson, $json);
    }

    public function test_groups()
    {
        $groups = [];

        $group = new Group('test');
        $groups[] = $group;

        $check1 = new Check();
        $check1->setId('test:success:check.a');
        $group->addCheck($check1->getId(), $check1);

        $group = new Group('test2', 'test2', 'test2');
        $groups[] = $group;

        $group->addCheck($check1->getId(), $check1);
        $check2 = new Check();
        $check2->setId('test:success:check.b');
        $group->addCheck($check2->getId(), $check2);

        $json = $this->serializer->serialize($groups, 'json');

        $this->assertJson($json);

        $expJson = <<<'JSON'
[
{"id":"test","name":"test","label":"test(1)","count":1,"checks":["test:success:check.a"]},
{"id":"test2","name":"test2","label":"test2(2)","descr":"test2","count":2,"checks":["test:success:check.a","test:success:check.b"]}
]
JSON;

        $this->assertJsonStringEqualsJsonString($expJson, $json);

    }
}
