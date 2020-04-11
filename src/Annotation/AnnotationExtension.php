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

use PHPUnit\Runner\BeforeTestHook;

class AnnotationExtension implements BeforeTestHook
{
    use Annotations;

    public function executeBeforeTest(string $test) : void
    {
        /** @var class-string $class */
        [$class, $method] = preg_split('/ |::/', $test);
        $this->processAnnotations($class, $method);
    }
}
