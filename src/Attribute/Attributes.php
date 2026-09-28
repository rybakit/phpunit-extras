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

namespace PHPUnitExtras\Attribute;

trait Attributes
{
    private ?AttributeProcessor $attributeProcessor = null;

    /** @var array<class-string, true> */
    private static array $processedClasses = [];

    protected function createAttributeProcessorBuilder() : AttributeProcessorBuilder
    {
        return AttributeProcessorBuilder::fromDefaults();
    }

    /**
     * @param class-string $class
     */
    public function processTestAttributes(string $class, string $method) : void
    {
        $classAttributes = $this->collectAttributes(new \ReflectionClass($class));

        if ($classAttributes && !isset(self::$processedClasses[$class])) {
            $this->getAttributeProcessor()->process($classAttributes, new Target($class));
            self::$processedClasses[$class] = true;
        }

        $methodAttributes = $this->collectAttributes(new \ReflectionMethod($class, $method));
        if ($methodAttributes) {
            $this->getAttributeProcessor()->process($methodAttributes, new Target($class, $method));
        }
    }

    /** @return list<ProcessableAttribute> */
    private function collectAttributes(\ReflectionClass|\ReflectionMethod $reflector) : array
    {
        $attributes = [];
        foreach ($reflector->getAttributes(ProcessableAttribute::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $instance = $attribute->newInstance();
            \assert($instance instanceof ProcessableAttribute);

            $attributes[] = $instance;
        }

        return $attributes;
    }

    protected function getAttributeProcessor() : AttributeProcessor
    {
        if ($this->attributeProcessor) {
            return $this->attributeProcessor;
        }

        $builder = $this->createAttributeProcessorBuilder();

        return $this->attributeProcessor = $builder->build();
    }
}
