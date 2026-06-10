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

use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;

final class AnnotationSubscriber implements PreparationStartedSubscriber
{
    private AnnotationExtension $extension;

    public function __construct(AnnotationExtension $extension)
    {
        $this->extension = $extension;
    }

    #[\Override]
    public function notify(PreparationStarted $event) : void
    {
        $test = $event->test();

        if (!method_exists($test, 'className') || !method_exists($test, 'methodName')) {
            return;
        }

        /** @var class-string $class */
        $class = $test->className();

        $this->extension->processTestAttributes($class, $test->methodName());
    }
}
