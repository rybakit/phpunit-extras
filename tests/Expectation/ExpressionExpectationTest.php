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

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Expectation\ExpressionContext;
use PHPUnitExtras\Expectation\ExpressionExpectation;
use PHPUnitExtras\Tests\PHPUnitCompat;

final class ExpressionExpectationTest extends TestCase
{
    use PHPUnitCompat;

    /** @var ExpressionContext|MockObject $context */
    private $context;

    protected function setUp() : void
    {
        $this->context = $this->createMock(ExpressionContext::class);
    }

    public function testVerifySucceeds() : void
    {
        $this->context->expects($this->atLeastOnce())->method('getExpression')->willReturn('a + b === 3');
        $this->context->expects($this->atLeastOnce())->method('getValues')->willReturn(['a' => 1, 'b' => 2]);

        $expectation = new ExpressionExpectation($this->context);
        $expectation->verify();
    }

    public function testVerifyFails() : void
    {
        $this->context->expects($this->atLeastOnce())->method('getExpression')->willReturn('a + b === 3');
        $this->context->expects($this->atLeastOnce())->method('getValues')->willReturn(['a' => 1, 'b' => 7]);

        $expectation = new ExpressionExpectation($this->context);

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/"a \+ b === 3".+\'a\' => 1.+\'b\' => 7.+is evaluated to true/s');
        $expectation->verify();
    }
}
