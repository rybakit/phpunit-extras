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

/**
 * Copied from doctrine/annotations.
 * @see https://github.com/doctrine/annotations/blob/master/lib/Doctrine/Annotations/ImplicitlyIgnoredAnnotationNames.php
 */
final class EstablishedAnnotationNames
{
    private const WidelyUsedNonStandard = [
        'fix'      => true,
        'fixme'    => true,
        'override' => true,
    ];

    private const PhpDocumentor1 = [
        'abstract'   => true,
        'access'     => true,
        'code'       => true,
        'deprec'     => true,
        'endcode'    => true,
        'exception'  => true,
        'final'      => true,
        'ingroup'    => true,
        'inheritdoc' => true,
        'inheritDoc' => true,
        'magic'      => true,
        'name'       => true,
        'private'    => true,
        'static'     => true,
        'staticvar'  => true,
        'staticVar'  => true,
        'toc'        => true,
        'tutorial'   => true,
        'throw'      => true,
    ];

    private const PhpDocumentor2 = [
        'api'            => true,
        'author'         => true,
        'category'       => true,
        'copyright'      => true,
        'deprecated'     => true,
        'example'        => true,
        'filesource'     => true,
        'global'         => true,
        'ignore'         => true,
        /* Can we enable this? 'index' => true, */
        'internal'       => true,
        'license'        => true,
        'link'           => true,
        'method'         => true,
        'package'        => true,
        'param'          => true,
        'property'       => true,
        'property-read'  => true,
        'property-write' => true,
        'return'         => true,
        'see'            => true,
        'since'          => true,
        'source'         => true,
        'subpackage'     => true,
        'throws'         => true,
        'todo'           => true,
        'TODO'           => true,
        'usedby'         => true,
        'uses'           => true,
        'var'            => true,
        'version'        => true,
    ];

    private const PHPUnit = [
        'author'                         => true,
        'after'                          => true,
        'afterClass'                     => true,
        'backupGlobals'                  => true,
        'backupStaticAttributes'         => true,
        'before'                         => true,
        'beforeClass'                    => true,
        'codeCoverageIgnore'             => true,
        'codeCoverageIgnoreStart'        => true,
        'codeCoverageIgnoreEnd'          => true,
        'covers'                         => true,
        'coversDefaultClass'             => true,
        'coversNothing'                  => true,
        'dataProvider'                   => true,
        'depends'                        => true,
        'doesNotPerformAssertions'       => true,
        'expectedException'              => true,
        'expectedExceptionCode'          => true,
        'expectedExceptionMessage'       => true,
        'expectedExceptionMessageRegExp' => true,
        'group'                          => true,
        'large'                          => true,
        'medium'                         => true,
        'preserveGlobalState'            => true,
        'requires'                       => true,
        'runTestsInSeparateProcesses'    => true,
        'runInSeparateProcess'           => true,
        'small'                          => true,
        'test'                           => true,
        'testdox'                        => true,
        'testWith'                       => true,
        'ticket'                         => true,
        'uses'                           => true,
    ];

    private const PhpCheckStyle = [
        'SuppressWarnings' => true,
    ];

    private const PhpStorm = [
        'noinspection' => true,
    ];

    private const PEAR = [
        'package_version' => true,
    ];

    private const PlainUML = [
        'startuml' => true,
        'enduml'   => true,
    ];

    private const Symfony = [
        'experimental' => true,
    ];

    private const PhpCodeSniffer = [
        'codingStandardsIgnoreStart' => true,
        'codingStandardsIgnoreEnd'   => true,
    ];

    private const SlevomatCodingStandard = [
        'phpcsSuppress' => true,
    ];

    public const LIST = self::WidelyUsedNonStandard
        + self::PhpDocumentor1
        + self::PhpDocumentor2
        + self::PHPUnit
        + self::PhpCheckStyle
        + self::PhpStorm
        + self::PEAR
        + self::PlainUML
        + self::Symfony
        + self::SlevomatCodingStandard
        + self::PhpCodeSniffer;

    private function __construct()
    {
    }
}
