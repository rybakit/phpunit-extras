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

final class ConstantRequirement implements Requirement
{
    public function getName() : string
    {
        return 'constant';
    }

    public function check(string $value) : ?string
    {
        if (\defined($value)) {
            return null;
        }

        return sprintf('The constant "%s" is undefined', $value);
    }
}
