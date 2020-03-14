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

final class ChainExpectation implements Expectation
{
    /**
     * @var array<int, Expectation>
     */
    private $expectations = [];

    public function expect(Expectation $expectation) : void
    {
        $this->expectations[] = $expectation;
    }

    public function verify() : void
    {
        try {
            foreach ($this->expectations as $expectation) {
                $expectation->verify();
            }
        } finally {
            $this->expectations = [];
        }
    }
}
