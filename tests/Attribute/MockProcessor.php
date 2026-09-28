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

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\Processor\Processor;
use PHPUnitExtras\Attribute\Target;

final class MockProcessor implements Processor
{
    public $lastProcessedValue;
    private string $attributeClass;

    public function __construct(string $attributeClass)
    {
        $this->attributeClass = $attributeClass;
    }

    public function getAttributeClasses() : array
    {
        return [$this->attributeClass];
    }

    public function process(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : void
    {
        $this->lastProcessedValue = $attribute;
    }
}
