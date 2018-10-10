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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use JMS\Serializer\Serializer;
use Tvi\MonitorBundle\Exception\HttpException;
use Tvi\MonitorBundle\Reporter\Api;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @property RunnerManager   $runnerManager
 * @property ReporterManager $reporterManager
 * @property Serializer      $serializer
 *
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait TraitApiCheck
{
    public function checkAction(Request $request, string $id): Response
    {
        try {
            $runner = $this->runnerManager->getRunner($id);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');
            $runner->addReporter($reporter);

            $runner->run();

            $res = $reporter->getCheckResults();

            if (isset($res[0])) {
                return $this->creatResponse($res[0], Response::HTTP_OK, true);
            }

            throw new NotFoundHttpException(sprintf('Check %s not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException($e->getStatusCode(), $e->getMessage());

            return $this->creatResponse($e->toArray(), $e->getStatusCode(), true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checksAction(Request $request): Response
    {
        try {
            list($ids, $checks, $groups, $tags) = $this->getFilterParams($request);
            $checks = $checks ? $checks : $ids;

            $runner = $this->runnerManager->getRunner($checks, $groups, $tags);

            $breakOnFailure = (bool) $request->get('break-on-failure', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $data = [
                'statusCode' => $reporter->getStatusCode(),
                'statusName' => $reporter->getStatusName(),

                'successes' => $reporter->getSuccessCount(),
                'warnings' => $reporter->getWarningCount(),
                'failures' => $reporter->getFailureCount(),
                'unknowns' => $reporter->getUnknownCount(),
                'total' => $reporter->getTotalCount(),

                'checks' => $reporter->getCheckResults(),
            ];

            return $this->creatResponse($data, Response::HTTP_OK, true);
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkStatusAction(Request $request, string $id): Response
    {
        try {
            $runner = $this->runnerManager->getRunner($id);

            $breakOnFailure = (bool) $request->get('break-on-failure', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $res = $reporter->getCheckResults();

            if (isset($res[0])) {
                $code = $reporter->getStatusCode() === $reporter::STATUS_CODE_SUCCESS
                    ? Response::HTTP_OK
                    : Response::HTTP_BAD_GATEWAY;

                return $this->creatResponse($reporter->getStatusName(), $code);
            }

            throw new NotFoundHttpException(sprintf('Check "%s" not found', $id));
        } catch (NotFoundHttpException $e) {
            return $this->creatResponse($e->getMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkStatusesAction(Request $request): Response
    {
        try {
            list($ids, $checks, $groups, $tags) = $this->getFilterParams($request);
            $checks = $checks ? $checks : $ids;

            $runner = $this->runnerManager->getRunner($checks, $groups, $tags);

            $breakOnFailure = (bool) $request->get('break-on-failure', false);
            $runner->setBreakOnFailure($breakOnFailure);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');
            $runner->addReporter($reporter);

            $runner->run();

            if ($reporter->getTotalCount()) {
                $code = $reporter->getStatusCode() === $reporter::STATUS_CODE_SUCCESS
                    ? Response::HTTP_OK
                    : Response::HTTP_BAD_GATEWAY;

                return $this->creatResponse($reporter->getStatusName(), $code);
            }

            throw new NotFoundHttpException('Check(s) not found');
        } catch (NotFoundHttpException $e) {
            return $this->creatResponse($e->getMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            return $this->creatResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
