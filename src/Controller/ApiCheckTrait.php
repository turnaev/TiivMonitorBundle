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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tvi\MonitorBundle\Exception\HttpException;
use Tvi\MonitorBundle\Reporter\Api;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @property RunnerManager   $runnerManager
 * @property ReporterManager $reporterManager
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait ApiCheckTrait
{
    public function checkAction(Request $request, string $check): JsonResponse
    {
        try {
            $runner = $this->runnerManager->getRunner($check);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $res = $reporter->getCheckResults();

            if (isset($res[0])) {
                return new JsonResponse($res[0]);
            }
            throw new NotFoundHttpException(sprintf('Check %s not found', $check));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException(404, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function checkListAction(Request $request): JsonResponse
    {
        try {
            list($checks, $groups, $tags) = $this->getFilterParams($request);

            $runner = $this->runnerManager->getRunner($checks, $groups, $tags);

            $breakOnFailure = (bool) $request->get('break-on-failure', false);
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
            $e = new HttpException(500, $e->getMessage());

            return new JsonResponse($e->toArray(), $e->getStatusCode());
        }
    }

    public function checkStatusAction(Request $request, ?string $checkSingle = null): Response
    {
        try {
            list($checks, $groups, $tags) = $this->getFilterParams($request);

            $checks = array_filter($checks, static function ($i) {
                return null !== $i;
            });

            if (null !== $checkSingle) {
                $runner = $this->runnerManager->getRunner($checkSingle);
            } else {
                $runner = $this->runnerManager->getRunner($checks, $groups, $tags);
            }

            $breakOnFailure = (bool) $request->get('break-on-failure', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            if ($reporter->getTotalCount()) {
                $code = $reporter->getStatusCode() === $reporter::STATUS_CODE_SUCCESS ? 200 : 500;

                return new Response($reporter->getStatusName(), $code);
            }

            throw new NotFoundHttpException('Check(s) not found');
        } catch (NotFoundHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 502);
        }
    }
}
