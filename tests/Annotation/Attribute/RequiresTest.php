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

namespace PHPUnitExtras\Tests\Annotation\Attribute;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\Attribute\Requires;

final class RequiresTest extends TestCase
{
    public function testGetNameReturnsRequiresProcessorName() : void
    {
        $attribute = new Requires('condition', 'server.FOO');

        self::assertSame('requires', $attribute->getName());
    }

    public function testGetValuePreservesRequirementTypeAndValue() : void
    {
        $attribute = new Requires('condition', 'server.FOO');

        self::assertSame('condition server.FOO', $attribute->getValue());
    }

    public function testGetValueSupportsRequirementWithoutValue() : void
    {
        $attribute = new Requires('extension');

        self::assertSame('extension', $attribute->getValue());
    }
}
