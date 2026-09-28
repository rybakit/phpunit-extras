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

use PHPUnit\Framework\TestCase;

#[Log('%tmp_dir%/%target_class%.log,class attribute 1')]
#[Log('%tmp_dir%/%target_class%.log,class attribute 2')]
final class AttributeExtensionTest extends TestCase
{
    public static function setUpBeforeClass() : void
    {
        @unlink(self::getLogFilename());
    }

    #[Log('%tmp_dir%/%target_class%.log,method attribute 1')]
    #[Log('%tmp_dir%/%target_class%.log,method attribute 2')]
    public function testAllAttributesAreProcessed() : void
    {
        $filename = self::getLogFilename();

        try {
            self::assertStringEqualsFile($filename,
                "class attribute 1\n".
                "class attribute 2\n".
                "method attribute 1\n".
                "method attribute 2\n"
            );
        } finally {
            @unlink($filename);
        }
    }

    private static function getLogFilename() : string
    {
        return \sprintf('%s/%s.log',
            sys_get_temp_dir(),
            (new \ReflectionClass(__CLASS__))->getShortName()
        );
    }
}
