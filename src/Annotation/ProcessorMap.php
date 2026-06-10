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

use PHPUnitExtras\Annotation\Processor\Processor;

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

    public function get(string $name) : Processor
    {
        if (isset($this->processors[$name])) {
            return $this->processors[$name];
        }

        throw InvalidAnnotationException::unknownName($name);
    }

    public function tryGet(string $name) : ?Processor
    {
        if (isset($this->processors[$name])) {
            return $this->processors[$name];
        }

        throw InvalidAnnotationException::unknownName($name);
    }

    private function addProcessor(Processor $processor) : void
    {
        $this->processors[$processor->getName()] = $processor;
    }
}
