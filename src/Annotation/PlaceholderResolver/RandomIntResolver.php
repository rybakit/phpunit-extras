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

namespace PHPUnitExtras\Annotation\PlaceholderResolver;

use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\Target;

final class RandomIntResolver implements PlaceholderResolver
{
    public function getName() : string
    {
        return 'random_int';
    }

    public function resolve(string $value, Target $target) : string
    {
        if (!preg_match('/%random_int\b(?P<args>[^%]*)%/', $value, $match)) {
            return $value;
        }

        if (!preg_match('/\(\s*(?P<min>[+-]?\d+)\s*,\s*(?P<max>[+-]?\d+)\)/', $match['args'], $args)) {
            throw InvalidAnnotationException::invalidSyntax($value, 'invalid argument format for placeholder %random_int%');
        }

        return (string) random_int((int) $args['min'], (int) $args['max']);
    }
}
