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
use PHPUnitExtras\Annotation\PlaceholderResolver\RandomIntResolver;
use PHPUnitExtras\Annotation\Target;

final class RandomIntResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveSubstitutesSupportedPlaceholdersData()
     */
    public function testResolveSubstitutesSupportedPlaceholders(string $value, array $expectedResults) : void
    {
        $resolver = new RandomIntResolver();
        $resolvedValue = $resolver->resolve($value, new Target('fooClass'));

        // prior to PHPUnit 9 assertContains() does loose comparison
        // self::assertContains($resolvedValue, $expectedResults);
        self::assertTrue(\in_array($resolvedValue, $expectedResults, true));
    }

    public function provideResolveSubstitutesSupportedPlaceholdersData() : iterable
    {
        return [
            ['[%random_int(-1, 1)%]', ['[-1]', '[0]', '[1]']],
            ['[%random_int(-1, +1)%]', ['[-1]', '[0]', '[1]']],
            ['[%random_int(-2, -1)%]', ['[-2]', '[-1]']],
            ['[%random_int(-1, -1)%]', ['[-1]']],
            ['[%random_int(+1, +1)%]', ['[1]']],
            ['[%random_int(+0, +0)%]', ['[0]']],
            ['[%random_int(+1, +2)%]', ['[1]', '[2]']],
            ['[%random_int(1, 2)%,%random_int(3, 4)%]', ['[1,3]', '[2,3]', '[1,4]', '[2,4]']],
            ['[%foobar%]', ['[%foobar%]']],
        ];
    }

    /**
     * @dataProvider provideResolveThrowsInvalidSyntaxErrorData()
     */
    public function testResolveThrowsInvalidSyntaxError(string $value) : void
    {
        $resolver = new RandomIntResolver();

        $this->expectException(InvalidAnnotationException::class);
        $this->expectExceptionMessage(sprintf('Unable to parse "%s": invalid argument format for placeholder %%random_int%%', $value));
        $resolver->resolve($value, new Target('fooClass'));
    }

    public function provideResolveThrowsInvalidSyntaxErrorData() : iterable
    {
        return [
            ['[%random_int%]'],
            ['[%random_int()%]'],
            ['[%random_int(a)%]'],
            ['[%random_int(a,b)%]'],
            ['[%random_int(,2)%]'],
            ['[%random_int(1,)%]'],
            ['[%random_int(1, 2, 3)%]'],
        ];
    }
}
