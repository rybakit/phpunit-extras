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

namespace PHPUnitExtras\Tests\Annotation\PlaceholderResolver;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Annotation\Target;

final class ChainResolverTest extends TestCase
{
    public function testResolveCallsSubResolvers() : void
    {
        $target = new Target('fooClass');

        $resolver1 = $this->createMock(PlaceholderResolver::class);
        $resolver1->expects(self::atLeastOnce())->method('getName')->willReturn('r1');
        $resolver1->expects(self::atLeastOnce())->method('resolve')->with('%foo%:%bar%', $target)
            ->willReturnCallback(static function ($value) { return 'FOO:%bar%'; });

        $resolver2 = $this->createMock(PlaceholderResolver::class);
        $resolver2->expects(self::atLeastOnce())->method('getName')->willReturn('r2');
        $resolver2->expects(self::atLeastOnce())->method('resolve')->with('FOO:%bar%', $target)
            ->willReturnCallback(static function ($value) { return 'FOO:BAR'; });

        $chainResolver = new ChainResolver([$resolver1, $resolver2]);

        self::assertSame('FOO:BAR', $chainResolver->resolve('%foo%:%bar%', $target));
    }

    public function testResolveFailsOnUnsupportedPlaceholder() : void
    {
        $value = '%foo%:%bar%';
        $target = new Target('fooClass');

        $resolver = $this->createMock(PlaceholderResolver::class);
        $resolver->expects(self::atLeastOnce())->method('getName')->willReturn('r');
        $resolver->method('resolve')->with($value, $target)->willReturn('FOO:%bar%');

        $chainResolver = new ChainResolver([$resolver]);

        $this->expectException(InvalidAnnotationException::class);
        $this->expectExceptionMessage('Unresolved placeholder "bar"');

        $chainResolver->resolve($value, $target);
    }
}
