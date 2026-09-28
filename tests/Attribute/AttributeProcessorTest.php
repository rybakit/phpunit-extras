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

namespace PHPUnitExtras\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\AttributeProcessor;
use PHPUnitExtras\Attribute\InvalidAttributeException;
use PHPUnitExtras\Attribute\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Attribute\ProcessorMap;
use PHPUnitExtras\Attribute\RequiresConstant;
use PHPUnitExtras\Attribute\RequiresIf;
use PHPUnitExtras\Attribute\Target;

final class AttributeProcessorTest extends TestCase
{
    public function testProcessProcessesMatchedAttributes() : void
    {
        $processor = new AttributeProcessor(new ProcessorMap([
            $foo = new MockProcessor(Log::class),
            $bar = new MockProcessor(RequiresIf::class),
            $baz = new MockProcessor(RequiresConstant::class),
        ]), new ChainResolver());

        $attributes = [$fooAttribute = new Log('foo'), $barAttribute = new RequiresIf('bar')];
        $processor->process($attributes, new Target('fooClass'));

        self::assertSame($fooAttribute, $foo->lastProcessedValue);
        self::assertSame($barAttribute, $bar->lastProcessedValue);
        self::assertNull($baz->lastProcessedValue);
    }

    public function testProcessThrowsExceptionOnUnknownAttribute() : void
    {
        $processorMap = new ProcessorMap([new MockProcessor(Log::class)]);
        $processor = new AttributeProcessor($processorMap, new ChainResolver());

        $attributes = [new Log('foo'), new RequiresIf('bar')];

        $this->expectException(InvalidAttributeException::class);
        $this->expectExceptionMessage(RequiresIf::class);
        $processor->process($attributes, new Target('fooClass'));
    }
}
