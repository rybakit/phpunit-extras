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

namespace PHPUnitExtras\Annotation\Processor\Requirement;

use Composer\Semver\Semver;

trait WithVersionChecker
{
    private function versionSatisfies(string $version, string $constraints) : bool
    {
        $constraints = self::normalizeVersionConstraints($constraints);

        return Semver::satisfies($version, $constraints);
    }

    private static function normalizeVersionConstraints(string $constraints) : string
    {
        if ('' === $constraints || !ctype_digit($constraints[0])) {
            return $constraints;
        }

        return implode('.', explode('.', $constraints, 3) + [1 => '*', 2 => '*']);
    }
}
