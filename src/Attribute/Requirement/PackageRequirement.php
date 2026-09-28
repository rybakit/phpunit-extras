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

namespace PHPUnitExtras\Attribute\Requirement;

use Composer\InstalledVersions;
use Composer\Semver\Semver;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\RequiresPackage;
use PHPUnitExtras\Attribute\Target;

final class PackageRequirement implements Requirement
{
    #[\Override]
    public function getAttributeClass() : string
    {
        return RequiresPackage::class;
    }

    #[\Override]
    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        if (!$attribute instanceof RequiresPackage) {
            throw new \InvalidArgumentException('PackageRequirement only handles RequiresPackage attributes');
        }

        $value = $placeholderResolver->resolve($attribute->package, $target);
        $parts = explode(' ', $value, 2);
        $packageName = $parts[0];
        $versionConstraints = $parts[1] ?? null;

        try {
            $packageVersion = InstalledVersions::getVersion($packageName);
        } catch (\OutOfBoundsException $e) {
            return \sprintf('Package "%s" is required', $value);
        }

        if (null === $versionConstraints || '' === $versionConstraints) {
            return null;
        }

        if (null === $packageVersion) {
            return \sprintf('"%s" version %s is required', $packageName, $versionConstraints);
        }

        $packageVersion = explode('@', $packageVersion, 2)[0];
        if (Semver::satisfies($packageVersion, $versionConstraints)) {
            return null;
        }

        return \sprintf('"%s" version %s is required', $packageName, $versionConstraints);
    }
}
