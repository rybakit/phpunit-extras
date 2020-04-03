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

final class EstablishedAnnotationNames
{
    public const PHPUNIT = [
        'author' => true,
        'after' => true,
        'afterClass' => true,
        'backupGlobals' => true,
        'backupStaticAttributes' => true,
        'before' => true,
        'beforeClass' => true,
        'codeCoverageIgnore' => true,
        'codeCoverageIgnoreStart' => true,
        'codeCoverageIgnoreEnd' => true,
        'covers' => true,
        'coversDefaultClass' => true,
        'coversNothing' => true,
        'dataProvider' => true,
        'depends' => true,
        'doesNotPerformAssertions' => true,
        'expectedException' => true,
        'expectedExceptionCode' => true,
        'expectedExceptionMessage' => true,
        'expectedExceptionMessageRegExp' => true,
        'group' => true,
        'large' => true,
        'medium' => true,
        'preserveGlobalState' => true,
        'preCondition' => true,
        'postCondition' => true,
        'requires' => true,
        'runTestsInSeparateProcesses' => true,
        'runInSeparateProcess' => true,
        'small' => true,
        'test' => true,
        'testdox' => true,
        'testWith' => true,
        'ticket' => true,
        'uses' => true,
    ];

    public const ALL = self::PHPUNIT;

    private function __construct()
    {
    }
}
