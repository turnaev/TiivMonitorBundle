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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Tvi\MonitorBundle\Runner\RunnerManager;

class UIController extends Controller
{
    /**
     * @var RunnerManager
     */
    protected $runnerManager;


    public function __construct(RunnerManager $runnerManager)
    {
        $this->runnerManager = $runnerManager;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, ?string $group = null)
    {
        $groups = $this->runnerManager->findGroups();
        $checks = $this->runnerManager->findChecks($group);

        return $this->render('@TviMonitor/ui/index.html.twig', [
                'groups' => $groups,
                'checks' => $checks,
            ]
        );
    }
}
