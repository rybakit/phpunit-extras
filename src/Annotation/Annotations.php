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

namespace PHPUnitExtras\Annotation;

use PHPUnit\Util\Test;

trait Annotations
{
    /** @var AnnotationProcessor|null */
    private $annotationProcessor;

    /** @var array<string, true> */
    private static $processedClasses = [];

    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return AnnotationProcessorBuilder::fromDefaults();
    }

    /**
     * @param class-string $class
     */
    private function processAnnotations(string $class, string $method) : void
    {
        $annotations = Test::parseTestMethodAnnotations($class, $method);

        if ($annotations['class'] && !isset(self::$processedClasses[$class])) {
            $this->getAnnotationProcessor()->process($annotations['class'], new Target($class));
            self::$processedClasses[$class] = true;
        }

        if ($annotations['method']) {
            $this->getAnnotationProcessor()->process($annotations['method'], new Target($class, $method));
        }
    }

    private function getAnnotationProcessor() : AnnotationProcessor
    {
        if ($this->annotationProcessor) {
            return $this->annotationProcessor;
        }

        $builder = $this->createAnnotationProcessorBuilder();

        return $this->annotationProcessor = $builder->build();
    }
}
