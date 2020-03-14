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
use PHPUnitExtras\Annotation\PlaceholderResolver\TargetClassResolver;
use PHPUnitExtras\Annotation\Target;

final class TargetClassResolverTest extends TestCase
{
    /**
     * @dataProvider provideResolveSubstitutesSupportedPlaceholdersData()
     */
    public function testResolveSubstitutesSupportedPlaceholders(string $value, Target $target, $expectedResult) : void
    {
        $resolver = new TargetClassResolver();

        self::assertSame($expectedResult, $resolver->resolve($value, $target));
    }

    public function provideResolveSubstitutesSupportedPlaceholdersData() : iterable
    {
        $classTarget = new Target(__CLASS__);
        $methodTarget = new Target(__CLASS__, __METHOD__);
        $resolvedValue = sprintf('[%s]', (new \ReflectionClass(__CLASS__))->getShortName());
        $resolvedFullValue = sprintf('[%s]', __CLASS__);

        return [
            ['[%target_class%]', $classTarget, $resolvedValue],
            ['[%target_class%]', $methodTarget, $resolvedValue],
            ['[%target_class_full%]', $classTarget, $resolvedFullValue],
            ['[%target_class_full%]', $methodTarget, $resolvedFullValue],
            ['[%foobar%]', $classTarget, '[%foobar%]'],
        ];
    }
}
