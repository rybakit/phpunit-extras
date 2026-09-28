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

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\RequiresConstant;
use PHPUnitExtras\Attribute\Target;

final class ConstantRequirement implements Requirement
{
    #[\Override]
    public function getAttributeClass() : string
    {
        return RequiresConstant::class;
    }

    #[\Override]
    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        if (!$attribute instanceof RequiresConstant) {
            throw new \InvalidArgumentException('ConstantRequirement only handles RequiresConstant attributes');
        }

        $constant = $placeholderResolver->resolve($attribute->constant, $target);

        if (\defined($constant)) {
            return null;
        }

        return \sprintf('The constant "%s" is undefined', $constant);
    }
}
