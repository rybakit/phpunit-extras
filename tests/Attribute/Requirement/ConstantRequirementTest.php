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

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Attribute\Requirement\ConstantRequirement;
use PHPUnitExtras\Attribute\RequiresConstant;
use PHPUnitExtras\Attribute\Target;

final class ConstantRequirementTest extends TestCase
{
    public function testCheckPassesForDefinedConstant() : void
    {
        $requirement = new ConstantRequirement();

        self::assertNull($requirement->check(new RequiresConstant('PHP_VERSION'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckFailsForUndefinedConstant() : void
    {
        $requirement = new ConstantRequirement();

        self::assertSame('The constant "FOOBAR" is undefined', $requirement->check(new RequiresConstant('FOOBAR'), new Target('foo'), new ChainResolver()));
    }
}
