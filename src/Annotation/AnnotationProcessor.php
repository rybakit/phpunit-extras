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

use PHPUnitExtras\Annotation\PlaceholderResolver\PlaceholderResolver;

final class AnnotationProcessor
{
    /**
     * @var ProcessorMap
     */
    private $processorMap;

    /**
     * @var PlaceholderResolver
     */
    private $placeholderResolver;

    public function __construct(ProcessorMap $processorMap, PlaceholderResolver $placeholderResolver)
    {
        $this->processorMap = $processorMap;
        $this->placeholderResolver = $placeholderResolver;
    }

    public function getPlaceholderResolver() : PlaceholderResolver
    {
        return $this->placeholderResolver;
    }

    public function process(array $annotations, Target $target) : void
    {
        foreach ($annotations as $name => $values) {
            if (!$processor = $this->processorMap->tryGet($name)) {
                continue;
            }

            foreach ($values as $value) {
                $value = $this->placeholderResolver->resolve($value, $target);
                $processor->process($value);
            }
        }
    }
}
