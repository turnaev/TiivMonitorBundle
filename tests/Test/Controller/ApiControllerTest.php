<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Test\Base\WebTestCase;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class ApiControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        $this->client = $this->getClient(false);
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * @dataProvider checkProvider
     */
    public function test_api_check($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.check', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $this->assertSame($code, $response->getStatusCode());

        $content = json_decode($content);

        foreach ($res as $f => $v) {
            $this->assertSame($v, $content->{$f}, sprintf('%s=%s', $f, $v));
        }

        if (Response::HTTP_NOT_FOUND === $code) {
            $this->assertObjectHasAttribute('Error', $content);
        }
    }

    /**
     * @dataProvider checksProvider
     */
    public function test_api_checks($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.check(s)', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
        $this->assertJson($content);

        $content = json_decode($content);

        foreach ($res as $f => $v) {
            $this->assertSame($v, $content->{$f}, sprintf('%s=%s', $f, $v));
        }
    }

    /**
     * @dataProvider checkStatusProvider
     */
    public function test_api_check_status($params, $code, $res)
    {
        $req = $this->router->generate('tvi_monitor.routing.api.checks_status', $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertSame($res, $content);
    }

    public function checkStatusProvider()
    {
        return [
            '.1' => [
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            '.2' => [
                ['break-on-failure' => 1],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'check.1' => [
                ['check' => 1],
                Response::HTTP_NOT_FOUND,
                'Check(s) not found',
            ],
            'check.2' => [
                ['checkSingle' => 'core:php_version'],
                Response::HTTP_OK,
                'SUCCESS',
            ],
            'check.3' => [
                ['check[1]' => 'core:php_version'],
                Response::HTTP_OK,
                'SUCCESS',
            ],
            'check.4' => [
                [
                    'check[1]' => 'core:php_version',
                    'check[2]' => 'core:php_version.a',
                    'check[3]' => 'core:php_version.b',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'group.1.' => [
                ['group' => 1],
                Response::HTTP_NOT_FOUND,
                'Check(s) not found',
            ],
            'group.2' => [
                ['group' => 'php'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'group.3' => [
                ['group' => 'php'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'group.3' => [
                ['group' => 'test:success'],
                Response::HTTP_OK,
                'SUCCESS',
            ],
            'group.4' => [
                ['group' => 'test:warning'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'WARNING',
            ],
            'group.5' => [
                ['group' => 'test:skip'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'SKIP',
            ],
            'group.6' => [
                ['group' => 'test:failure'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'tag.1' => [
                ['tag' => 1],
                Response::HTTP_NOT_FOUND,
                'Check(s) not found',
            ],
            'tag.2.' => [
                ['tag' => 'all'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'FAILURE',
            ],
            'tag.3' => [
                ['tag[1]' => 'ok'],
                Response::HTTP_OK,
                'SUCCESS',
            ],
            'tag.4' => [
                ['tag[1]' => 'skip'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'SKIP',
            ],
            'tag.5' => [
                ['tag[1]' => 'warning'],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                'WARNING',
            ],
        ];
    }

    public function checksProvider()
    {
        return [
            '.1' => [
                [],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 5,
                    'warnings' => 3,
                    'failures' => 6,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],
            '.2' => [
                ['break-on-failure' => 1],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 2,
                    'warnings' => 0,
                    'failures' => 1,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],

            'check.1' => [
                ['check' => 1],
                Response::HTTP_OK,
                [
                    'total' => 0,
                ],
            ],
            'check.2' => [
                ['check' => 'core:php_version'],
                Response::HTTP_OK,
                [
                    'total' => 1,
                ],
            ],
            'check.2' => [
                ['check[]' => 'core:php_version'],
                Response::HTTP_OK,
                [
                    'total' => 1,
                ],
            ],
            'check.3' => [
                [
                    'check[1]' => 'core:php_version',
                    'check[2]' => 'core:php_version.a',
                    'check[3]' => 'core:php_version.b',
                ],
                Response::HTTP_OK,
                [
                    'total' => 3,
                ],
            ],
            'group.1.' => [
                ['group' => 1],
                Response::HTTP_OK,
                [
                    'total' => 0,
                ],
            ],
            'group.2' => [
                ['group' => 'php'],
                Response::HTTP_OK,
                [
                    'total' => 3,
                ],
            ],
            'group.3' => [
                ['group[1]' => 'php', 'group[2]' => 'core', 'group[3]' => 'test:success'],
                Response::HTTP_OK,
                [
                    'total' => 6,
                ],
            ],
            'tag.1' => [
                ['tag' => 1],
                Response::HTTP_OK,
                [
                    'total' => 0,
                ],
            ],
            'tag.2.' => [
                ['tag' => 'all'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 5,
                    'warnings' => 3,
                    'failures' => 6,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],
            'tag.3.' => [
                ['tag[1]' => 'core', 'tag[2]' => 'ok'],
                Response::HTTP_OK,
                [
                    'total' => 6,
                ],
            ],
        ];
    }

    public function checkProvider()
    {
        return [
            'not_exist.1' => [
                ['check' => 'not_exist'],
                Response::HTTP_NOT_FOUND,
                [],
            ],
            '.1' => [
                ['check' => 'core:php_version'],
                Response::HTTP_OK,
                [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'core:php_version',
                    'group' => 'php',
                ],
            ],
            '.2' => [
                ['check' => 'core:php_version.a'],
                Response::HTTP_OK,
                [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'core:php_version.a',
                    'group' => 'php',
                ],
            ],
            '.3' => [
                ['check' => 'core:php_version.b'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'core:php_version.b',
                    'group' => 'php',
                ],
            ],
            '.4' => [
                ['check' => 'test:success:check'],
                Response::HTTP_OK,
                [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check',
                    'group' => 'test:success',
                ],
            ],
            '.5' => [
                ['check' => 'test:success:check.a'],
                Response::HTTP_OK,
                [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check.a',
                    'group' => 'test:success',
                ],
            ],
            '.6' => [
                ['check' => 'test:success:check.b'],
                Response::HTTP_OK,
                [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check.b',
                    'group' => 'test:success',
                ],
            ],
            '.7' => [
                ['check' => 'test:skip:check'],
                Response::HTTP_OK,
                [
                    'statusCode' => Response::HTTP_OK,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check',
                    'group' => 'test:skip',
                ],
            ],
            '.8' => [
                ['check' => 'test:skip:check.a'],
                Response::HTTP_OK,
                [
                    'statusCode' => 200,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check.a',
                    'group' => 'test:skip',
                ],
            ],
            '.9' => [
                ['check' => 'test:skip:check.b'],
                Response::HTTP_OK,
                [
                    'statusCode' => Response::HTTP_OK,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check.b',
                    'group' => 'test:skip',
                ],
            ],
            '.10' => [
                ['check' => 'test:warning:check'],
                Response::HTTP_OK,
                [

                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check',
                    'group' => 'test:warning',
                ],
            ],
            '.11' => [
                ['check' => 'test:warning:check.a'],
                Response::HTTP_OK,
                [
                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check.a',
                    'group' => 'test:warning',
                ],
            ],
            '.12' => [
                ['check' => 'test:warning:check.b'],
                Response::HTTP_OK,
                [
                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check.b',
                    'group' => 'test:warning',
                ],
            ],
            '.13' => [
                ['check' => 'test:failure:check'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check',
                    'group' => 'test:failure',
                ],
            ],
            '.14' => [
                ['check' => 'test:failure:check.0'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.0',
                    'group' => 'test:failure',
                ],
            ],
            '.15' => [
                ['check' => 'test:failure:check.1'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.1',
                    'group' => 'test:failure1',
                ],
            ],
            '.16' => [
                ['check' => 'test:failure:check.2'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.2',
                    'group' => 'test:failure2',
                ],
            ],
            '.17' => [
                ['check' => 'test:failure:check.3'],
                Response::HTTP_OK,
                [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.3',
                    'group' => 'test:failure3',
                ],
            ],
        ];
    }
}
