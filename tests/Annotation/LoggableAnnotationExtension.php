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

namespace PHPUnitExtras\Tests\Annotation;

use PHPUnitExtras\Annotation\AnnotationExtension;
use PHPUnitExtras\Annotation\AnnotationProcessorBuilder;

final class LoggableAnnotationExtension extends AnnotationExtension
{
    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return parent::createAnnotationProcessorBuilder()
            ->addProcessor(new LogProcessor());
    }
}
