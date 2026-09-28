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

namespace PHPUnitExtras\Tests\Attribute\Requirement;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Attribute\Requirement\IfRequirement;
use PHPUnitExtras\Attribute\RequiresIf;
use PHPUnitExtras\Attribute\Target;

final class IfRequirementTest extends TestCase
{
    public function testCheckPassesForTruthyExpression() : void
    {
        $requirement = new IfRequirement(['foo' => 42]);

        self::assertNull($requirement->check(new RequiresIf('foo === 42'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckFailsForFalsyExpression() : void
    {
        $requirement = new IfRequirement(['foo' => 42]);

        self::assertSame('"foo === 12" is not evaluated to true', $requirement->check(new RequiresIf('foo === 12'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckPassesForTruthyExpressionUsingGlobalContext() : void
    {
        $requirement = IfRequirement::fromGlobals();

        self::assertNull($requirement->check(new RequiresIf('server.REQUEST_TIME > 0'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckFailsForFalsyExpressionUsingGlobalContext() : void
    {
        $requirement = IfRequirement::fromGlobals();
        $expr = 'server.REQUEST_TIME < 0';

        self::assertSame("\"$expr\" is not evaluated to true", $requirement->check(new RequiresIf($expr), new Target('foo'), new ChainResolver()));
    }

    #[DataProvider('provideSupportedGlobals')]
    public function testCheckEvaluatesMissingKeyInGlobalContextToNull(string $globalName) : void
    {
        $requirement = IfRequirement::fromGlobals();
        $expr = "$globalName.__MISSING_KEY__";

        self::assertSame("\"$expr\" is not evaluated to true", $requirement->check(new RequiresIf($expr), new Target('foo'), new ChainResolver()));
    }

    public static function provideSupportedGlobals() : iterable
    {
        return [
            ['cookie'],
            ['env'],
            ['get'],
            ['files'],
            ['post'],
            ['request'],
            ['server'],
        ];
    }

    public function testCheckPassesForTruthyExpressionUsingFunction() : void
    {
        $requirement = IfRequirement::fromGlobals();

        self::assertNull($requirement->check(new RequiresIf('false !== strpos(server.argv[0], "phpunit")'), new Target('foo'), new ChainResolver()));
    }
}
