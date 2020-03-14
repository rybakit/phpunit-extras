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
use PHPUnitExtras\Annotation\PlaceholderResolver\TargetMethodResolver;
use PHPUnitExtras\Annotation\Target;

final class TargetMethodResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveSubstitutesSupportedPlaceholdersData()
     */
    public function testResolveSubstitutesSupportedPlaceholders(string $value, Target $target, $expectedResult) : void
    {
        $resolver = new TargetMethodResolver();

        self::assertSame($expectedResult, $resolver->resolve($value, $target));
    }

    public function provideResolveSubstitutesSupportedPlaceholdersData() : iterable
    {
        $classTarget = new Target(__CLASS__);
        $methodTarget = new Target(__CLASS__, 'testResolveSubstitutesPlaceholders');
        $resolvedValue = sprintf('[%s]', 'ResolveSubstitutesPlaceholders');
        $resolvedFullValue = sprintf('[%s]', 'testResolveSubstitutesPlaceholders');

        return [
            ['[%target_method%]', $classTarget, '[%target_method%]'],
            ['[%target_method%]', $methodTarget, $resolvedValue],
            ['[%target_method_full%]', $classTarget, '[%target_method_full%]'],
            ['[%target_method_full%]', $methodTarget, $resolvedFullValue],
            ['[%foobar%]', $methodTarget, '[%foobar%]'],
        ];
    }
}
