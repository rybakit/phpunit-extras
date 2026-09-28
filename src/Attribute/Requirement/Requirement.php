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
use PHPUnitExtras\Attribute\Target;

interface Requirement
{
    /** @return class-string<ProcessableAttribute> */
    public function getAttributeClass() : string;

    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string;
}
