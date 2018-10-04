<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Exception\HttpException;
use Tvi\MonitorBundle\Reporter\Api;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class ApiController
{
    /**
     * @var RunnerManager
     */
    protected $runnerManager;

    /**
     * @var ReporterManager
     */
    protected $reporterManager;

    public function __construct(RunnerManager $runnerManager, ReporterManager $reporterManager)
    {
        $this->runnerManager = $runnerManager;
        $this->reporterManager = $reporterManager;
    }

    public function checkListAction(Request $request): JsonResponse
    {
        try {
            list($checks, $groups, $tags) = $this->getFilterParams($request);

            $runner = $this->runnerManager->getRunner($checks, $groups, $tags);

            $breakOnFailure = (bool) $request->get('bof', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            return new JsonResponse([
                'statusCode' => $reporter->getStatusCode(),
                'statusName' => $reporter->getStatusName(),

                'successes' => $reporter->getSuccessCount(),
                'warnings' => $reporter->getWarningCount(),
                'failures' => $reporter->getFailureCount(),
                'unknowns' => $reporter->getUnknownCount(),
                'total' => $reporter->getTotalCount(),

                'checks' => $reporter->getCheckResults(),
            ]);
        } catch (\Exception $e) {
            $e = new HttpException(404, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function checkAction(Request $request, string $check): JsonResponse
    {
        try {
            $runner = $this->runnerManager->getRunner($check);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $res = $reporter->getCheckResults()[0];

            return new JsonResponse($res);
        } catch (\Exception $e) {
            $e = new HttpException(404, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function checkStatusAction(Request $request, ?string $check = null): Response
    {
        try {

            list($checks, $groups, $tags) = $this->getFilterParams($request);

            if($check !== null) {
                $runner = $this->runnerManager->getRunner($check);
            } else {
                $runner = $this->runnerManager->getRunner($checks, $groups, $tags);
            }

            $breakOnFailure = (bool) $request->get('bof', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $code = $reporter->getStatusCode() == $reporter::STATUS_CODE_SUCCESS ? 200 : 500;

            return new Response($reporter->getStatusName(), $code);

        } catch (\Exception $e) {
            $e = new HttpException(404, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    /**
     * @return array [$checks, $groups, $tags],
     */
    private function getFilterParams(Request $request): array
    {
        $checks = $request->get('check', []);
        if (\is_scalar($checks)) {
            $checks = $checks ? [$checks] : [];
        }
        $checks = !\is_array($checks) ? [$checks] : $checks;

        $groups = $request->get('group', []);
        if (\is_scalar($groups)) {
            $groups = $groups ? [$groups] : [];
        }
        $groups = !\is_array($groups) ? [$groups] : $groups;

        $tags = $request->get('tag', []);
        if (\is_scalar($tags)) {
            $tags = $tags ? [$tags] : [];
        }
        $tags = !\is_array($tags) ? [$tags] : $tags;

        return [$checks, $groups, $tags];
    }
}
