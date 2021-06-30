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

namespace PHPUnitExtras\Annotation;

use PHPUnit\Framework\Exception;

/** @psalm-suppress InternalMethod */
final class InvalidAnnotationException extends Exception
{
    public static function unknownName(string $name) : self
    {
        return new self(sprintf('Unknown annotation "%s"', $name));
    }

    public static function invalidSyntax(string $annotation, string $reason = '') : self
    {
        return new self(sprintf('Unable to parse "%s": %s', $annotation, $reason));
    }

    public static function unresolvedPlaceholder(string $placeholder) : self
    {
        return new self(sprintf('Unresolved placeholder "%s"', $placeholder));
    }

    public static function unknownRequirement(string $requirement) : self
    {
        return new self(sprintf('Unknown requirement "%s"', $requirement));
    }
}
