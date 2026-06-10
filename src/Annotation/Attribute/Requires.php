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

namespace PHPUnitExtras\Annotation\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Requires implements AnnotationAttribute
{
    private string $type;
    private string $value;

    public function __construct(string $type, string $value = '')
    {
        $this->type = $type;
        $this->value = $value;
    }

    #[\Override]
    public function getName() : string
    {
        return 'requires';
    }

    #[\Override]
    public function getValue() : string
    {
        return trim("$this->type $this->value");
    }
}
