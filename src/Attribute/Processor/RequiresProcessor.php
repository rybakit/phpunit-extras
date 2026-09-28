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

namespace PHPUnitExtras\Attribute\Processor;

use PHPUnit\Framework\Assert;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\Requirement\Requirement;
use PHPUnitExtras\Attribute\Target;

final class RequiresProcessor implements Processor
{
    /** @var array<class-string<ProcessableAttribute>, Requirement> */
    private $requirements = [];

    /**
     * @param array<array-key, Requirement> $requirements
     */
    public function __construct(array $requirements)
    {
        foreach ($requirements as $requirement) {
            $this->requirements[$requirement->getAttributeClass()] = $requirement;
        }
    }

    #[\Override]
    public function getAttributeClasses() : array
    {
        return array_keys($this->requirements);
    }

    #[\Override]
    public function process(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : void
    {
        $requirement = $this->requirements[$attribute::class];
        if (null !== $error = $requirement->check($attribute, $target, $placeholderResolver)) {
            Assert::markTestSkipped($error);
        }
    }
}
