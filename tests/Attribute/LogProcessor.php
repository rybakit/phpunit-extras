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

final class LogProcessor implements Processor
{
    public function getAttributeClasses() : array
    {
        return [Log::class];
    }

    public function process(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : void
    {
        \assert($attribute instanceof Log);
        $value = $placeholderResolver->resolve($attribute->value, $target);
        [$filename, $data] = explode(',', $value, 2);
        file_put_contents($filename, "$data\n", \FILE_APPEND);
    }
}
