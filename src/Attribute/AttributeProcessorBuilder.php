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

use PHPUnitExtras\Attribute\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\PlaceholderResolver\TargetClassResolver;
use PHPUnitExtras\Attribute\PlaceholderResolver\TargetMethodResolver;
use PHPUnitExtras\Attribute\PlaceholderResolver\TmpDirResolver;
use PHPUnitExtras\Attribute\Processor\Processor;
use PHPUnitExtras\Attribute\Processor\RequiresProcessor;
use PHPUnitExtras\Attribute\Requirement\ConstantRequirement;
use PHPUnitExtras\Attribute\Requirement\IfRequirement;
use PHPUnitExtras\Attribute\Requirement\PackageRequirement;
use PHPUnitExtras\Attribute\Requirement\Requirement;

final class AttributeProcessorBuilder
{
    /** @var list<Processor> */
    private $processors = [];

    /** @var array<string, Requirement> */
    private $requirements = [];

    /** @var array<string, PlaceholderResolver> */
    private $placeholderResolvers = [];

    public static function fromDefaults() : self
    {
        return (new self())
            ->addRequirement(IfRequirement::fromGlobals())
            ->addRequirement(new ConstantRequirement())
            ->addRequirement(new PackageRequirement())
            ->addPlaceholderResolver(new TargetClassResolver())
            ->addPlaceholderResolver(new TargetMethodResolver())
            ->addPlaceholderResolver(new TmpDirResolver())
        ;
    }

    public function addProcessor(Processor $processor) : self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function addRequirement(Requirement $requirement) : self
    {
        $this->requirements[$requirement->getAttributeClass()] = $requirement;

        return $this;
    }

    public function addPlaceholderResolver(PlaceholderResolver $resolver) : self
    {
        $this->placeholderResolvers[$resolver->getName()] = $resolver;

        return $this;
    }

    public function build() : AttributeProcessor
    {
        $processors = $this->processors;

        if ($this->requirements) {
            array_unshift($processors, new RequiresProcessor($this->requirements));
        }

        return new AttributeProcessor(
            new ProcessorMap($processors),
            new ChainResolver($this->placeholderResolvers)
        );
    }
}
