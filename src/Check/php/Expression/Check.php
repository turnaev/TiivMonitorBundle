<?php

/**
 * This file is part of the `tvi/monitor-bundle` project.
 *
 * (c) https://github.com/turnaev/monitor-bundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Tvi\MonitorBundle\Check\php\Expression;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tvi\MonitorBundle\Exception\FeatureRequired;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;
use ZendDiagnostics\Result\Failure;

use Tvi\MonitorBundle\Check\CheckInterface;
use Tvi\MonitorBundle\Check\CheckTrait;

/**
 * @author Vladimir Turnaev <turnaev@gmail.com>
 */
class Check extends \ZendDiagnostics\Check\AbstractCheck implements CheckInterface
{
    use CheckTrait;

    /**
     * @var Expression|string
     */
    private $warningExpression;

    /**
     * @var Expression|string
     */
    private $criticalExpression;

    /**
     * @var ?string
     */
    private $warningMessage;

    /**
     * @var ?string
     */
    private $criticalMessage;

    /**
     * @param Expression|string $warningExpression
     * @param Expression|string $criticalExpression
     * @param ?string           $warningMessage
     * @param ?string           $criticalMessage
     *
     * @throws \Exception
     */
    public function __construct($warningExpression = null, $criticalExpression = null, $warningMessage = null, $criticalMessage = null)
    {
        if (!class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
            throw new FeatureRequired('The symfony/expression-language is required for this check.');
        }

        if (!$warningExpression && !$criticalExpression) {
            throw new \InvalidArgumentException('Not checks set.');
        }

        $this->warningExpression = $warningExpression;
        $this->warningMessage = $warningMessage;
        $this->criticalExpression = $criticalExpression;
        $this->criticalMessage = $criticalMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $language = $this->getExpressionLanguage();

        if ($this->criticalExpression && false === $language->evaluate($this->criticalExpression)) {
            return new Failure($this->criticalMessage);
        }

        if ($this->warningExpression && false === $language->evaluate($this->warningExpression)) {
            return new Warning($this->warningMessage);
        }

        return new Success();
    }

    protected function getExpressionLanguage()
    {
        $language = new ExpressionLanguage();
        $language->register('ini', static function ($value) {
            return $value;
        }, static function ($arguments, $value) {
            return ini_get($value);
        });

        return $language;
    }
}
