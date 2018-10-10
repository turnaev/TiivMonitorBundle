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

use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Tvi\MonitorBundle\Reporter\ReporterManager;
use Tvi\MonitorBundle\Runner\RunnerManager;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
trait TraitApiCommon
{
    /**
     * @var RunnerManager
     */
    protected $runnerManager;

    /**
     * @var ReporterManager
     */
    protected $reporterManager;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(RunnerManager $runnerManager, ReporterManager $reporterManager, Serializer $serializer)
    {
        $this->runnerManager = $runnerManager;
        $this->reporterManager = $reporterManager;
        $this->serializer = $serializer;
    }

    protected function creatResponse($data = null, int $status = Response::HTTP_OK, bool $json = false, array $headers = []): Response
    {
        if ($json && !\is_string($json)) {
            $data = $this->serializer->serialize($data, 'json');
        }

        if ($json) {
            $headers['Content-Type'] = 'application/json';
        }

        return new Response($data, $status, $headers);
    }
}
