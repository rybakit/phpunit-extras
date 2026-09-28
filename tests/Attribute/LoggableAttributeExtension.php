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

use PHPUnitExtras\Attribute\AttributeExtension;
use PHPUnitExtras\Attribute\AttributeProcessorBuilder;

final class LoggableAttributeExtension extends AttributeExtension
{
    protected function createAttributeProcessorBuilder() : AttributeProcessorBuilder
    {
        return parent::createAttributeProcessorBuilder()
            ->addProcessor(new LogProcessor());
    }
}
