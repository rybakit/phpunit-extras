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

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;

final class AttributeProcessor
{
    private ProcessorMap $processorMap;
    private PlaceholderResolver $placeholderResolver;

    public function __construct(ProcessorMap $processorMap, PlaceholderResolver $placeholderResolver)
    {
        $this->processorMap = $processorMap;
        $this->placeholderResolver = $placeholderResolver;
    }

    public function getPlaceholderResolver() : PlaceholderResolver
    {
        return $this->placeholderResolver;
    }

    /** @param list<ProcessableAttribute> $attributes */
    public function process(array $attributes, Target $target) : void
    {
        foreach ($attributes as $attribute) {
            $processor = $this->processorMap->get($attribute::class);
            $processor->process($attribute, $target, $this->placeholderResolver);
        }
    }
}
