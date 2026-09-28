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

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase as BaseTestCase;
use PHPUnitExtras\Attribute\Attributes;
use PHPUnitExtras\Attribute\Target;
use PHPUnitExtras\Expectation\Expectations;

abstract class TestCase extends BaseTestCase
{
    use Attributes;
    use Expectations;

    #[Before]
    final protected function processTestCaseAttributes() : void
    {
        $this->processTestAttributes(static::class, $this->name());
    }

    final protected function resolvePlaceholders(string $value) : string
    {
        $resolver = $this->getAttributeProcessor()->getPlaceholderResolver();

        return $resolver->resolve($value, Target::fromTestCase($this));
    }

    #[After]
    final protected function verifyTestCaseExpectations() : void
    {
        $this->verifyExpectations();
    }
}
