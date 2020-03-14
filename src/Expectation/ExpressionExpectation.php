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

use PHPUnit\Framework\Assert;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionExpectation implements Expectation
{
    /** @var ExpressionContext */
    private $context;

    /** @var ExpressionLanguage */
    private $language;

    public function __construct(ExpressionContext $context, ?ExpressionLanguage $language = null)
    {
        $this->context = $context;
        $this->language = $language ?: new ExpressionLanguage();
    }

    public function verify() : void
    {
        $expression = $this->context->getExpression();
        $values = $this->context->getValues();

        Assert::assertThat(
            $this->language->evaluate($expression, $values),
            new IsTruthyExpression($this->context)
        );
    }
}
