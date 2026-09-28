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

use PHPUnitExtras\Attribute\Processor\Processor;

final class ProcessorMap
{
    /** @var array<string, Processor> */
    private $processors = [];

    /**
     * @param array<array-key, Processor> $processors
     */
    public function __construct(array $processors)
    {
        foreach ($processors as $processor) {
            $this->addProcessor($processor);
        }
    }

    public function get(string $attributeClass) : Processor
    {
        if (isset($this->processors[$attributeClass])) {
            return $this->processors[$attributeClass];
        }

        throw InvalidAttributeException::unknownName($attributeClass);
    }

    private function addProcessor(Processor $processor) : void
    {
        foreach ($processor->getAttributeClasses() as $attributeClass) {
            $this->processors[$attributeClass] = $processor;
        }
    }
}
