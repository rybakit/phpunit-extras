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

namespace PHPUnitExtras\Tests\Annotation\Requirement;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\Requirement\ConditionRequirement;

final class ConditionRequirementTest extends TestCase
{
    public function testCheckPassesForTruthyExpression() : void
    {
        $requirement = new ConditionRequirement(['foo' => 42]);

        self::assertNull($requirement->check('foo === 42'));
    }

    public function testCheckFailsForFalsyExpression() : void
    {
        $requirement = new ConditionRequirement(['foo' => 42]);

        self::assertSame('"foo === 12" is not evaluated to true', $requirement->check('foo === 12'));
    }

    public function testCheckPassesForTruthyExpressionUsingGlobalContext() : void
    {
        $requirement = ConditionRequirement::fromGlobals();

        self::assertNull($requirement->check('server.REQUEST_TIME > 0'));
    }

    public function testCheckPassesForTruthyExpressionUsingFunction() : void
    {
        $requirement = ConditionRequirement::fromGlobals();

        self::assertNull($requirement->check('false !== strpos(server.argv[0], "phpunit")'));
    }
}
