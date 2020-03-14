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

namespace PHPUnitExtras\Tests\Annotation\Processor\Requirement;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\Processor\Requirement\PackageRequirement;

final class PackageRequirementTest extends TestCase
{
    public function testCheckPassesForInstalledPackage() : void
    {
        $requirement = new PackageRequirement();

        self::assertNull($requirement->check('composer/semver'));
    }

    public function testCheckFailsForMissingPackage() : void
    {
        $requirement = new PackageRequirement();

        self::assertSame('Package "foo/bar" is required', $requirement->check('foo/bar'));
    }

    public function testCheckPassesForCompliantPackageVersion() : void
    {
        $requirement = new PackageRequirement();

        self::assertNull($requirement->check('composer/semver ^1.0|^2.0|^3.0'));
    }

    public function testCheckFailsForNonCompliantPackageVersion() : void
    {
        $requirement = new PackageRequirement();

        self::assertSame('"composer/semver" version ^42.0 is required', $requirement->check('composer/semver ^42.0'));
    }
}
