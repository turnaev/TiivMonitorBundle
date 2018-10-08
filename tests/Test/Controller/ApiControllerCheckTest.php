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
class ApiControllerCheckTest extends WebTestCase
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
    public function test_api_check($route, $params, $code, $res)
    {
        $req = $this->router->generate($route, $params, false);

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
     * @dataProvider checkStatusesProvider
     */
    public function test_api_check_status($route, $params, $code, $res)
    {
        $req = $this->router->generate($route, $params, false);
        $this->client->request('GET', $req);

        $response = $this->client->getResponse();
        $content = $response->getContent();

        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame('text/html; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertSame($res, $content);
    }

    /**
     * @return array [$route, $params, $code, $res]
     */
    public function checkStatusesProvider()
    {
        return [
            'statuses.1' => [
                'tvi_monitor.routing.api.check_status(s)', [],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.2' => [
                'tvi_monitor.routing.api.check_status(s)', ['break-on-failure' => 1],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.check.1' => [
                'tvi_monitor.routing.api.check_status(s)', ['check' => 1],
                Response::HTTP_NOT_FOUND, 'Check(s) not found',
            ],
            'statuses.check.3' => [
                'tvi_monitor.routing.api.check_status(s)', ['check[1]' => 'core:php_version'],
                Response::HTTP_OK, 'SUCCESS',
            ],
            'statuses.check.4' => [
                'tvi_monitor.routing.api.check_status(s)', [
                    'check[1]' => 'core:php_version',
                    'check[2]' => 'core:php_version.a',
                    'check[3]' => 'core:php_version.b',
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.group.1.' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 1],
                Response::HTTP_NOT_FOUND, 'Check(s) not found',
            ],
            'statuses.group.2' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'php'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.group.3' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'php'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.group.3' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'test:success'],
                Response::HTTP_OK, 'SUCCESS',
            ],
            'statuses.group.4' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'test:warning'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'WARNING',
            ],
            'statuses.group.5' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'test:skip'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'SKIP',
            ],
            'statuses.group.6' => [
                'tvi_monitor.routing.api.check_status(s)', ['group' => 'test:failure'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.tag.1' => [
                'tvi_monitor.routing.api.check_status(s)', ['tag' => 1],
                Response::HTTP_NOT_FOUND, 'Check(s) not found',
            ],
            'statuses.tag.2.' => [
                'tvi_monitor.routing.api.check_status(s)', ['tag' => 'all'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
            'statuses.tag.3' => [
                'tvi_monitor.routing.api.check_status(s)', ['tag[1]' => 'ok'],
                Response::HTTP_OK, 'SUCCESS',
            ],
            'statuses.tag.4' => [
                'tvi_monitor.routing.api.check_status(s)', ['tag[1]' => 'skip'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'SKIP',
            ],
            'statuses.tag.5' => [
                'tvi_monitor.routing.api.check_status(s)', ['tag[1]' => 'warning'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'WARNING',
            ],
            'status.check.1' => [
                'tvi_monitor.routing.api.check_status', ['id' => 'core:php_version'],
                Response::HTTP_OK, 'SUCCESS',
            ],
            'status.check.2' => [
                'tvi_monitor.routing.api.check_status', ['id' => 'test:success:check'],
                Response::HTTP_OK, 'SUCCESS',
            ],
            'status.check.3' => [
                'tvi_monitor.routing.api.check_status', ['id' => 'test:warning:check'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'WARNING',
            ],
            'status.check.4' => [
                'tvi_monitor.routing.api.check_status', ['id' => 'test:skip:check'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'SKIP',
            ],
            'status.check.5' => [
                'tvi_monitor.routing.api.check_status', ['id' => 'test:failure:check'],
                Response::HTTP_INTERNAL_SERVER_ERROR, 'FAILURE',
            ],
        ];
    }

    /**
     * @return array [$route, $params, $code, $res]
     */
    public function checkProvider()
    {
        return [
            'checks.1' => [
                'tvi_monitor.routing.api.check(s)', [],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 5,
                    'warnings' => 3,
                    'failures' => 6,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],
            'checks.2' => [
                'tvi_monitor.routing.api.check(s)', ['break-on-failure' => 1],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 2,
                    'warnings' => 0,
                    'failures' => 1,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],
            'checks.3' => [
                'tvi_monitor.routing.api.check(s)', ['check' => 1],
                Response::HTTP_OK, ['total' => 0],
            ],
            'checks.4' => [
                'tvi_monitor.routing.api.check(s)', ['check' => 'core:php_version'],
                Response::HTTP_OK, ['total' => 1],
            ],
            'checks.5' => [
                'tvi_monitor.routing.api.check(s)', ['check[]' => 'core:php_version'],
                Response::HTTP_OK, ['total' => 1],
            ],
            'checks.6' => [
                'tvi_monitor.routing.api.check(s)', [
                    'check[1]' => 'core:php_version',
                    'check[2]' => 'core:php_version.a',
                    'check[3]' => 'core:php_version.b',
                ],
                Response::HTTP_OK, ['total' => 3],
            ],
            'checks.group.1.' => [
                'tvi_monitor.routing.api.check(s)', ['group' => 1],
                Response::HTTP_OK, ['total' => 0],
            ],
            'checks.group.2' => [
                'tvi_monitor.routing.api.check(s)', ['group' => 'php'],
                Response::HTTP_OK, ['total' => 3],
            ],
            'checks.group.3' => [
                'tvi_monitor.routing.api.check(s)', ['group[1]' => 'php', 'group[2]' => 'core', 'group[3]' => 'test:success'],
                Response::HTTP_OK, ['total' => 6],
            ],
            'checks.tag.1' => [
                'tvi_monitor.routing.api.check(s)', ['tag' => 1],
                Response::HTTP_OK, ['total' => 0],
            ],
            'checks.tag.2.' => [
                'tvi_monitor.routing.api.check(s)', ['tag' => 'all'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'successes' => 5,
                    'warnings' => 3,
                    'failures' => 6,
                    'unknowns' => 0,
                    'total' => 17,
                ],
            ],
            'checks.tag.3.' => [
                'tvi_monitor.routing.api.check(s)', ['tag[1]' => 'core', 'tag[2]' => 'ok'],
                Response::HTTP_OK, ['total' => 6],
            ],
            'check.not_exist.1' => [
                'tvi_monitor.routing.api.check', ['id' => 'not_exist'],
                Response::HTTP_NOT_FOUND, [],
            ],
            'check.1' => [
                'tvi_monitor.routing.api.check', ['id' => 'core:php_version'],
                Response::HTTP_OK, [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'core:php_version',
                    'group' => 'php',
                ],
            ],
            'check.2' => [
                'tvi_monitor.routing.api.check', ['id' => 'core:php_version.a'],
                Response::HTTP_OK, [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'core:php_version.a',
                    'group' => 'php',
                ],
            ],
            'check.3' => [
                'tvi_monitor.routing.api.check', ['id' => 'core:php_version.b'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'core:php_version.b',
                    'group' => 'php',
                ],
            ],
            'check.4' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:success:check'],
                Response::HTTP_OK, [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check',
                    'group' => 'test:success',
                ],
            ],
            'check.5' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:success:check.a'],
                Response::HTTP_OK, [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check.a',
                    'group' => 'test:success',
                ],
            ],
            'check.6' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:success:check.b'],
                Response::HTTP_OK, [
                    'statusCode' => 0,
                    'statusName' => 'SUCCESS',
                    'check' => 'test:success:check.b',
                    'group' => 'test:success',
                ],
            ],
            'check.7' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:skip:check'],
                Response::HTTP_OK, [
                    'statusCode' => Response::HTTP_OK,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check',
                    'group' => 'test:skip',
                ],
            ],
            'check.8' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:skip:check.a'],
                Response::HTTP_OK, [
                    'statusCode' => 200,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check.a',
                    'group' => 'test:skip',
                ],
            ],
            'check.9' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:skip:check.b'],
                Response::HTTP_OK, [
                    'statusCode' => Response::HTTP_OK,
                    'statusName' => 'SKIP',
                    'check' => 'test:skip:check.b',
                    'group' => 'test:skip',
                ],
            ],
            'check.10' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:warning:check'],
                Response::HTTP_OK, [
                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check',
                    'group' => 'test:warning',
                ],
            ],
            'check.11' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:warning:check.a'],
                Response::HTTP_OK, [
                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check.a',
                    'group' => 'test:warning',
                ],
            ],
            'check.12' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:warning:check.b'],
                Response::HTTP_OK, [
                    'statusCode' => 100,
                    'statusName' => 'WARNING',
                    'check' => 'test:warning:check.b',
                    'group' => 'test:warning',
                ],
            ],
            'check.13' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:failure:check'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check',
                    'group' => 'test:failure',
                ],
            ],
            'check.14' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:failure:check.0'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.0',
                    'group' => 'test:failure',
                ],
            ],
            'check.15' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:failure:check.1'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.1',
                    'group' => 'test:failure1',
                ],
            ],
            'check.16' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:failure:check.2'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.2',
                    'group' => 'test:failure2',
                ],
            ],
            'check.17' => [
                'tvi_monitor.routing.api.check', ['id' => 'test:failure:check.3'],
                Response::HTTP_OK, [
                    'statusCode' => 1000,
                    'statusName' => 'FAILURE',
                    'check' => 'test:failure:check.3',
                    'group' => 'test:failure3',
                ],
            ],
        ];
    }
}
