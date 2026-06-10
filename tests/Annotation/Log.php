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

use PHPUnitExtras\Annotation\Attribute\AnnotationAttribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Log implements AnnotationAttribute
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getName() : string
    {
        return 'log';
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
