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
use PHPUnitExtras\Attribute\Requirement\PackageRequirement;
use PHPUnitExtras\Attribute\RequiresPackage;
use PHPUnitExtras\Attribute\Target;

final class PackageRequirementTest extends TestCase
{
    public function testCheckPassesForInstalledPackage() : void
    {
        $requirement = new PackageRequirement();

        self::assertNull($requirement->check(new RequiresPackage('composer/semver'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckFailsForMissingPackage() : void
    {
        $requirement = new PackageRequirement();

        self::assertSame('Package "foo/bar" is required', $requirement->check(new RequiresPackage('foo/bar'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckPassesForCompliantPackageVersion() : void
    {
        $requirement = new PackageRequirement();

        self::assertNull($requirement->check(new RequiresPackage('composer/semver ^1.0|^2.0|^3.0'), new Target('foo'), new ChainResolver()));
    }

    public function testCheckFailsForNonCompliantPackageVersion() : void
    {
        $requirement = new PackageRequirement();

        self::assertSame('"composer/semver" version ^42.0 is required', $requirement->check(new RequiresPackage('composer/semver ^42.0'), new Target('foo'), new ChainResolver()));
    }
}
