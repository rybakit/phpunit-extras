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

namespace PHPUnitExtras\Tests;

/**
 * A compatibility layer for the legacy PHPUnit 7.
 */
trait PHPUnitCompat
{
    public function expectExceptionMessageMatches(string $regularExpression) : void
    {
        \is_callable('parent::expectExceptionMessageMatches')
            ? parent::expectExceptionMessageMatches($regularExpression)
            : parent::expectExceptionMessageRegExp($regularExpression);
    }
}
