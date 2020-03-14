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

namespace PHPUnitExtras;

use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnitExtras\Annotation\Target;
use PHPUnitExtras\Annotation\WithAnnotations;
use PHPUnitExtras\Expectation\WithExpectations;

abstract class TestCase extends BaseTestCase
{
    use WithAnnotations;
    use WithExpectations;

    /**
     * @before
     */
    final protected function processTestCaseAnnotations() : void
    {
        $this->processAnnotations(\get_class($this), $this->getName(false) ?? '');
    }

    final protected function resolveAnnotationPlaceholders(string $value) : string
    {
        $resolver = $this->getAnnotationProcessor()->getPlaceholderResolver();

        return $resolver->resolve($value, Target::fromTestCase($this));
    }
}
