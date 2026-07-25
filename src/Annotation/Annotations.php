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

use PHPUnitExtras\Annotation\Attribute\AnnotationAttribute;

trait Annotations
{
    private ?AnnotationProcessor $annotationProcessor = null;

    /** @var array<string, true> */
    private array $processedClasses = [];

    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return AnnotationProcessorBuilder::fromDefaults();
    }

    /**
     * @param class-string $class
     */
    public function processTestAttributes(string $class, string $method) : void
    {
        $classAttributes = $this->collectAttributes(new \ReflectionClass($class));

        if ($classAttributes && !isset($this->processedClasses[$class])) {
            $this->getAnnotationProcessor()->process($classAttributes, new Target($class));
            $this->processedClasses[$class] = true;
        }

        $methodAttributes = $this->collectAttributes(new \ReflectionMethod($class, $method));
        if ($methodAttributes) {
            $this->getAnnotationProcessor()->process($methodAttributes, new Target($class, $method));
        }
    }

    /**
     * @return array<string, list<string>>
     */
    private function collectAttributes(\ReflectionClass|\ReflectionMethod $reflector) : array
    {
        $annotations = [];
        foreach ($reflector->getAttributes(AnnotationAttribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $instance = $attribute->newInstance();
            \assert($instance instanceof AnnotationAttribute);

            $annotations[$instance->getName()][] = $instance->getValue();
        }

        return $annotations;
    }

    protected function getAnnotationProcessor() : AnnotationProcessor
    {
        if ($this->annotationProcessor) {
            return $this->annotationProcessor;
        }

        $builder = $this->createAnnotationProcessorBuilder();

        return $this->annotationProcessor = $builder->build();
    }
}
