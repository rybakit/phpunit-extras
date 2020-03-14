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
use PHPUnitExtras\Annotation\PlaceholderResolver\TmpDirResolver;
use PHPUnitExtras\Annotation\Target;

final class TmpDirResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveSubstitutesSupportedPlaceholderData
     */
    public function testResolveSubstitutesSupportedPlaceholder(string $value, $expectedResult) : void
    {
        $resolver = new TmpDirResolver();

        self::assertSame($expectedResult, $resolver->resolve($value, new Target('fooClass')));
    }

    public function provideResolveSubstitutesSupportedPlaceholderData() : iterable
    {
        return [
            ['[%tmp_dir%]', '['.sys_get_temp_dir().']'],
            ['[%foobar%]', '[%foobar%]'],
        ];
    }
}
