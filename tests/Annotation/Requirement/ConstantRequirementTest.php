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
use PHPUnitExtras\Annotation\Requirement\ConstantRequirement;

final class ConstantRequirementTest extends TestCase
{
    public function testCheckPassesForDefinedConstant() : void
    {
        $requirement = new ConstantRequirement();

        self::assertNull($requirement->check('PHP_VERSION'));
    }

    public function testCheckFailsForUndefinedConstant() : void
    {
        $requirement = new ConstantRequirement();

        self::assertSame('The constant "FOOBAR" is undefined', $requirement->check('FOOBAR'));
    }
}
