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

use PHPUnitExtras\Annotation\Target;

final class TargetMethodResolver implements PlaceholderResolver
{
    public function getName() : string
    {
        return 'target_method';
    }

    public function resolve(string $value, Target $target) : string
    {
        if (null === $methodName = $target->tryGetMethodName()) {
            return $value;
        }

        return strtr($value, [
            '%target_method%' => $target->getMethodShortName(),
            '%target_method_full%' => $methodName,
        ]);
    }
}
