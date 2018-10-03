<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Test\Reporter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\AbstractResult;
use ZendDiagnostics\Result\Collection;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Skip;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;
use Tvi\MonitorBundle\Reporter\Mailer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>, Vladimir Turnaev <turnaev@gmail.com>
 *
 * @internal
 */
class MailerReporterTest extends TestCase
{
    /**
     * @dataProvider sendNoEmailProvider
     */
    public function test_send_no_email(ResultInterface $result, $sendOnWarning)
    {
        $mailer = $this->prophesize('Swift_Mailer');
        $mailer->send()->shouldNotBeCalled();

        $results = new Collection();
        $results[$this->prophesize('ZendDiagnostics\Check\CheckInterface')->reveal()] = $result;

        $reporter = new Mailer($mailer->reveal(), 'foo@bar.com', 'bar@foo.com', 'foo bar', $sendOnWarning);
        $reporter->onFinish($results);
    }

    /**
     * @dataProvider sendEmailProvider
     */
    public function test_send_email(ResultInterface $result, $sendOnWarning)
    {
        $mailer = $this->prophesize('Swift_Mailer');
        $mailer->send(Argument::type('Swift_Message'))->shouldBeCalled();

        $results = new Collection();
        $results[$this->prophesize(CheckInterface::class)->reveal()] = $result;

        $reporter = new Mailer($mailer->reveal(), 'foo@bar.com', 'bar@foo.com', 'foo bar', $sendOnWarning);
        $reporter->onFinish($results);
    }

    public function sendEmailProvider()
    {
        return [
            [new Failure(), true],
            [new Warning(), true],
            [new Unknown(), true],
            [new Failure(), false],
        ];
    }

    public function sendNoEmailProvider()
    {
        return [
            [new Success(), true],
            [new Skip(), true],
            [new Warning(), false],
        ];
    }
}

class Unknown extends AbstractResult
{
}
