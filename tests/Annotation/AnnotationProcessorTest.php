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

namespace PHPUnitExtras\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\AnnotationProcessor;
use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Annotation\ProcessorMap;
use PHPUnitExtras\Annotation\Target;

final class AnnotationProcessorTest extends TestCase
{
    public function testProcessProcessesMatchedAnnotations() : void
    {
        $processor = new AnnotationProcessor(new ProcessorMap([
            $foo = new MockProcessor('foo'),
            $bar = new MockProcessor('bar'),
            $baz = new MockProcessor('baz'),
        ]), new ChainResolver());

        $annotations = [
            'foo' => ['foo_value'],
            'bar' => ['bar_value'],
        ];
        $processor->process($annotations, new Target('fooClass'));

        self::assertSame($foo->lastProcessedValue, $annotations['foo'][0]);
        self::assertSame($bar->lastProcessedValue, $annotations['bar'][0]);
        self::assertNull($baz->lastProcessedValue);
    }

    public function testProcessThrowsExceptionOnUnknownAnnotation() : void
    {
        $processorMap = new ProcessorMap([new MockProcessor('foo')]);
        $processor = new AnnotationProcessor($processorMap, new ChainResolver());

        $annotations = [
            'foo' => ['foo_value'],
            'bar' => ['bar_value'],
        ];

        $this->expectException(InvalidAnnotationException::class);
        $this->expectExceptionMessage('Unknown annotation "bar"');
        $processor->process($annotations, new Target('fooClass'));
    }

    public function testProcessIgnoresUnknownAnnotation() : void
    {
        $processorMap = new ProcessorMap([$foo = new MockProcessor('foo')], [], true);
        $processor = new AnnotationProcessor($processorMap, new ChainResolver());

        $annotations = [
            'foo' => ['foo_value'],
            'bar' => ['bar_value'],
        ];

        $processor->process($annotations, Target::fromTestCase($this));

        self::assertSame($foo->lastProcessedValue, $annotations['foo'][0]);
    }

    public function testProcessIgnoresIgnoredAnnotations() : void
    {
        $processorMap = new ProcessorMap([$bar = new MockProcessor('bar')], ['foo', 'baz']);
        $processor = new AnnotationProcessor($processorMap, new ChainResolver());

        $annotations = [
            'foo' => ['foo_value'],
            'bar' => ['bar_value'],
            'baz' => ['baz_value'],
        ];

        $processor->process($annotations, new Target('fooClass'));

        self::assertSame($bar->lastProcessedValue, $annotations['bar'][0]);
    }
}
