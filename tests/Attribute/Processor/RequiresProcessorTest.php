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

namespace PHPUnitExtras\Tests\Attribute\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\PlaceholderResolver\ChainResolver;
use PHPUnitExtras\Attribute\Processor\RequiresProcessor;
use PHPUnitExtras\Attribute\Requirement\Requirement;
use PHPUnitExtras\Attribute\RequiresIf;
use PHPUnitExtras\Attribute\Target;

final class RequiresProcessorTest extends TestCase
{
    public function testProcessProcessesRequirement() : void
    {
        $attribute = new RequiresIf('foo === 42');
        $target = new Target('foo');
        $placeholderResolver = new ChainResolver();
        $requirement = $this->createMock(Requirement::class);
        $requirement->expects(self::once())->method('getAttributeClass')->willReturn(RequiresIf::class);
        $requirement->expects(self::once())->method('check')->with($attribute, $target, $placeholderResolver)->willReturn(null);

        $processor = new RequiresProcessor([$requirement]);
        $processor->process($attribute, $target, $placeholderResolver);
    }
}
