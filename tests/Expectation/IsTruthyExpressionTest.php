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

namespace PHPUnitExtras\Tests\Expectation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Expectation\ExpressionContext;
use PHPUnitExtras\Expectation\IsTruthyExpression;

final class IsTruthyExpressionTest extends TestCase
{
    /** @var ExpressionContext|MockObject */
    private $context;

    protected function setUp() : void
    {
        $this->context = $this->createMock(ExpressionContext::class);
    }

    public function testEvaluateSucceeds() : void
    {
        $constraint = new IsTruthyExpression($this->context);

        self::assertTrue($constraint->evaluate(true, '', true));
    }

    public function testEvaluateFails() : void
    {
        $constraint = new IsTruthyExpression($this->context);

        self::assertFalse($constraint->evaluate(false, '', true));
    }
}
