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

namespace PHPUnitExtras\Tests\Annotation\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\Processor\RequiresProcessor;
use PHPUnitExtras\Annotation\Requirement\Requirement;

final class RequiresProcessorTest extends TestCase
{
    public function testProcessProcessesRequirement() : void
    {
        $requirement = $this->createMock(Requirement::class);
        $requirement->expects($this->once())->method('getName')->willReturn('Foobar');
        $requirement->expects($this->once())->method('check')->with('42')->willReturn(null);

        $processor = new RequiresProcessor([$requirement]);
        $processor->process('Foobar 42');
    }

    public function testProcessProcessesRequirementWithoutValue() : void
    {
        $requirement = $this->createMock(Requirement::class);
        $requirement->expects($this->once())->method('getName')->willReturn('Foobar');
        $requirement->expects($this->once())->method('check')->with('')->willReturn(null);

        $processor = new RequiresProcessor([$requirement]);
        $processor->process('Foobar');
    }

    public function testProcessFailsOnUnknownRequirement() : void
    {
        $requirement = $this->createMock(Requirement::class);
        $requirement->method('getName')->willReturn('Foobar');

        $processor = new RequiresProcessor([$requirement]);

        $this->expectException(InvalidAnnotationException::class);
        $this->expectExceptionMessage('Unknown requirement "Bazqux"');
        $processor->process('Bazqux 42');
    }

    /**
     * @doesNotPerformAssertions
     * @dataProvider provideProcessSkipsPhpUnitRequirementsData
     */
    public function testProcessSkipsPhpUnitRequirements(string $phpunitRequirement) : void
    {
        $requirement = $this->createMock(Requirement::class);
        $requirement->method('getName')->willReturn('Foobar');

        $processor = new RequiresProcessor([$requirement]);
        $processor->process($phpunitRequirement);
    }

    public function provideProcessSkipsPhpUnitRequirementsData() : iterable
    {
        return [
            ['PHP 8.0'],
            ['PHPUnit < 9'],
            ['OS Linux'],
            ['OSFAMILY Solaris'],
            ['function imap_open'],
            ['extension mysqli'],
        ];
    }
}
