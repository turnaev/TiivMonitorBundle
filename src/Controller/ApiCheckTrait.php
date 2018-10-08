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
trait ApiCheckTrait
{
    public function checkAction(Request $request, string $id): JsonResponse
    {
        try {
            $runner = $this->runnerManager->getRunner($id);

            /** @var $reporter Api */
            $reporter = $this->reporterManager->getReporter('api');

            $runner->addReporter($reporter);
            $runner->run();

            $res = $reporter->getCheckResults();

            if (isset($res[0])) {
                return JsonResponse::fromJsonString($this->serializer->serialize($res[0], 'json'));
            }

            throw new NotFoundHttpException(sprintf('Check %s not found', $id));
        } catch (NotFoundHttpException $e) {
            $e = new HttpException(404, $e->getMessage());
            $json = $this->serializer->serialize($e->toArray(), 'json');

            return JsonResponse::fromJsonString($json, $e->getStatusCode());
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());

            $data = $this->serializer->serialize($e->toArray(), 'json');

            return JsonResponse::fromJsonString($data, $e->getStatusCode());
        }
    }

    public function checksAction(Request $request): JsonResponse
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
            $json = $this->serializer->serialize($data, 'json');

            return JsonResponse::fromJsonString($json);
        } catch (\Exception $e) {
            $e = new HttpException(500, $e->getMessage());
            $json = $this->serializer->serialize($e->toArray(), 'json');

            return JsonResponse::fromJsonString($json, $e->getStatusCode());
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

            if ($reporter->getTotalCount()) {
                $code = $reporter->getStatusCode() === $reporter::STATUS_CODE_SUCCESS ? 200 : 500;

                return new Response($reporter->getStatusName(), $code);
            }

            throw new NotFoundHttpException(sprintf('Check %s not found', $id));
        } catch (NotFoundHttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 502);
        }
    }

    public function checkStatusesAction(Request $request): Response
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
