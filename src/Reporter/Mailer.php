<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Reporter;

use Swift_Mailer;
use Swift_Message;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Collection as ResultsCollection;
use ZendDiagnostics\Result\ResultInterface;

/**
 * @author louis <louis@systemli.org>, turnaev valimir
 */
class Mailer extends ReporterAbstract
{
    private $mailer;
    private $recipient;
    private $subject;
    private $sender;
    private $sendOnWarning;

    /**
     * @param string $recipient
     * @param string $sender
     * @param string $subject
     * @param bool   $sendOnWarning
     */
    public function __construct(Swift_Mailer $mailer, $recipient, $sender, $subject, $sendOnWarning = true)
    {
        $this->mailer = $mailer;
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->subject = $subject;
        $this->sendOnWarning = $sendOnWarning;
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
        parent::onFinish($results);

        if ($results->getUnknownCount() > 0) {
            $this->sendEmail($results);

            return;
        }

        if ($results->getWarningCount() > 0 && $this->sendOnWarning) {
            $this->sendEmail($results);

            return;
        }

        if ($results->getFailureCount() > 0) {
            $this->sendEmail($results);

            return;
        }
    }

    private function sendEmail(ResultsCollection $results)
    {
        $body = '';

        foreach ($results as $check) {
            /* @var $check  CheckInterface */
            /* @var $result ResultInterface */
            $result = $results[$check] ?? null;

            if ($result instanceof ResultInterface) {
                $body .= sprintf("Check: %s\n", $check->getLabel());
                $body .= sprintf("Message: %s\n\n", $result->getMessage());
            }
        }

        $message = (new Swift_Message())
            ->setSubject($this->subject)
            ->setFrom($this->sender)
            ->setTo($this->recipient)
            ->setBody($body);

        $this->mailer->send($message);
    }
}
