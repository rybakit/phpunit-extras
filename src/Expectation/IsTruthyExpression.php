<?php

/**
 * This file is part of the rybakit/phpunit-extras package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PHPUnitExtras\Expectation;

use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Exporter\Exporter;

final class IsTruthyExpression extends Constraint
{
    private $context;

    /** @var Exporter|null */
    private $compatExporter;

    public function __construct(ExpressionContext $context)
    {
        $this->context = $context;
    }

    public function toString() : string
    {
        return 'is evaluated to true';
    }

    protected function matches($other) : bool
    {
        return true === $other;
    }

    protected function failureDescription($other) : string
    {
        return sprintf(
            "\"%s\" with values\n    %s\n%s",
            $this->context->getExpression(),
            $this->exporter()->export($this->context->getValues(), 1),
            $this->toString()
        );
    }

    /**
     * Needed for backward compatibility with PHPUnit 7.
     */
    protected function exporter() : Exporter
    {
        if (null === $this->compatExporter) {
            $this->compatExporter = new Exporter();
        }

        return $this->compatExporter;
    }
}
