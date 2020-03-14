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

namespace PHPUnitExtras\Expectation;

use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

interface Expectation
{
    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function verify() : void;
}
