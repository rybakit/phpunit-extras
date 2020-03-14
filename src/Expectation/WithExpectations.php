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

trait WithExpectations
{
    /** @var ChainExpectation|null */
    private $expectations;

    /**
     * @after
     */
    final protected function verifyExpectations() : void
    {
        $this->getExpectations()->verify();
    }

    final protected function expect(Expectation $expectation) : void
    {
        $this->getExpectations()->expect($expectation);
    }

    private function getExpectations() : ChainExpectation
    {
        if ($this->expectations) {
            return $this->expectations;
        }

        return $this->expectations = new ChainExpectation();
    }
}
