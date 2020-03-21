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

use PHPUnitExtras\Annotation\AnnotationProcessorBuilder;
use PHPUnitExtras\TestCase;

/**
 * @log %tmp_dir%/%target_class%.log,class annotation 1
 * @log %tmp_dir%/%target_class%.log,class annotation 2
 */
final class AnnotationTestCaseTest extends TestCase
{
    public static function setUpBeforeClass() : void
    {
        @unlink(self::getLogFilename());
    }

    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        return parent::createAnnotationProcessorBuilder()
            ->addProcessor(new LogProcessor());
    }

    /**
     * @log %tmp_dir%/%target_class%.log,method annotation 1
     * @log %tmp_dir%/%target_class%.log,method annotation 2
     */
    public function testAllAnnotationsAreProcessed() : void
    {
        $filename = self::getLogFilename();

        try {
            self::assertStringEqualsFile($filename,
                "class annotation 1\n".
                "class annotation 2\n".
                "method annotation 1\n".
                "method annotation 2\n"
            );
        } finally {
            @unlink($filename);
        }
    }

    public function testResolvePlaceholdersSubstitutesAllPlaceholders() : void
    {
        self::assertSame(
            self::getLogFilename(),
            $this->resolvePlaceholders('%tmp_dir%/%target_class%.log')
        );
    }

    private static function getLogFilename() : string
    {
        return sprintf('%s/%s.log',
            sys_get_temp_dir(),
            (new \ReflectionClass(__CLASS__))->getShortName()
        );
    }
}
