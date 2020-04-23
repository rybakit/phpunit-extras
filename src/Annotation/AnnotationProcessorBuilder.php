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

use PHPUnitExtras\Annotation\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\RandomIntResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\TargetClassResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\TargetMethodResolver;
use PHPUnitExtras\Annotation\PlaceholderResolver\TmpDirResolver;
use PHPUnitExtras\Annotation\Processor\Processor;
use PHPUnitExtras\Annotation\Processor\RequiresProcessor;
use PHPUnitExtras\Annotation\Requirement\ConditionRequirement;
use PHPUnitExtras\Annotation\Requirement\ConstantRequirement;
use PHPUnitExtras\Annotation\Requirement\PackageRequirement;
use PHPUnitExtras\Annotation\Requirement\Requirement;

final class AnnotationProcessorBuilder
{
    /** @var array<string, Processor> */
    private $processors = [];

    /** @var array<string, Requirement> */
    private $requirements = [];

    /** @var array<string, PlaceholderResolver> */
    private $placeholderResolvers = [];

    /** @var array<string, true> */
    private $ignoredAnnotations = [];

    /** @var bool */
    private $ignoreUnknownAnnotations = false;

    public static function fromDefaults() : self
    {
        return (new self())
            ->ignoreEstablishedAnnotations()
            ->addRequirement(ConditionRequirement::fromGlobals())
            ->addRequirement(new ConstantRequirement())
            ->addRequirement(new PackageRequirement())
            ->addPlaceholderResolver(new RandomIntResolver())
            ->addPlaceholderResolver(new TargetClassResolver())
            ->addPlaceholderResolver(new TargetMethodResolver())
            ->addPlaceholderResolver(new TmpDirResolver())
        ;
    }

    public function addProcessor(Processor $processor) : self
    {
        if ($processor instanceof RequiresProcessor) {
            $this->requirements = $processor->getRequirements() + $this->requirements;
        } else {
            $this->processors[$processor->getName()] = $processor;
        }

        return $this;
    }

    public function addRequirement(Requirement $requirement) : self
    {
        $this->requirements[$requirement->getName()] = $requirement;

        return $this;
    }

    public function addPlaceholderResolver(PlaceholderResolver $resolver) : self
    {
        $this->placeholderResolvers[$resolver->getName()] = $resolver;

        return $this;
    }

    public function ignoreUnknownAnnotations(bool $ignore = true) : self
    {
        $this->ignoreUnknownAnnotations = $ignore;

        return $this;
    }

    public function ignoreAnnotation(string $name) : self
    {
        $this->ignoredAnnotations[$name] = true;

        return $this;
    }

    public function ignoreEstablishedAnnotations() : self
    {
        $this->ignoredAnnotations = EstablishedAnnotationNames::ALL + $this->ignoredAnnotations;

        return $this;
    }

    public function build() : AnnotationProcessor
    {
        $processors = $this->processors;

        if ($this->requirements) {
            $requiresAnnotation = new RequiresProcessor($this->requirements);
            $processors = [$requiresAnnotation->getName() => $requiresAnnotation] + $processors;
        }

        return new AnnotationProcessor(
            new ProcessorMap($processors, array_keys($this->ignoredAnnotations), $this->ignoreUnknownAnnotations),
            new ChainResolver($this->placeholderResolvers)
        );
    }
}
