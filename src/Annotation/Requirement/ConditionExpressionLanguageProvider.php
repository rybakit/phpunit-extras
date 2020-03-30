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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class ConditionExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions() : array
    {
        return [
            ExpressionFunction::fromPhp('strtoupper'),
            ExpressionFunction::fromPhp('strtolower'),
            ExpressionFunction::fromPhp('strpos'),
        ];
    }
}
