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

namespace PHPUnitExtras\Annotation\Requirement;

use Composer\Semver\Semver;
use PackageVersions\Versions;

final class PackageRequirement implements Requirement
{
    public function getName() : string
    {
        return 'package';
    }

    public function check(string $value) : ?string
    {
        /**
         * @var string $packageName
         * @see https://github.com/vimeo/psalm/issues/3118
         */
        [$packageName, $versionConstraints] = explode(' ', $value, 2) + [1 => null];

        try {
            $packageVersion = Versions::getVersion($packageName);
        } catch (\OutOfBoundsException $e) {
            return sprintf('Package "%s" is required', $value);
        }

        if (!$versionConstraints) {
            return null;
        }

        $packageVersion = explode('@', $packageVersion, 2)[0];
        if (Semver::satisfies($packageVersion, $versionConstraints)) {
            return null;
        }

        return sprintf('"%s" version %s is required', $packageName, $versionConstraints);
    }
}
