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

namespace PHPUnitExtras\Attribute;

final class InvalidAttributeException extends \RuntimeException
{
    public static function unknownName(string $name) : self
    {
        return new self(\sprintf('Unknown attribute "%s"', $name));
    }

    public static function unresolvedPlaceholder(string $placeholder) : self
    {
        return new self(\sprintf('Unresolved placeholder "%s"', $placeholder));
    }
}
