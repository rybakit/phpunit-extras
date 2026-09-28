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

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class AttributeExtension implements Extension
{
    use Attributes;

    #[\Override]
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters) : void
    {
        $facade->registerSubscriber(new AttributeSubscriber($this));
    }
}
