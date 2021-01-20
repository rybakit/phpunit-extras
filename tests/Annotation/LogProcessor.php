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

use PHPUnitExtras\Annotation\Processor\Processor;

final class LogProcessor implements Processor
{
    public function getName() : string
    {
        return 'log';
    }

    public function process(string $value) : void
    {
        [$filename, $data] = explode(',', $value, 2);
        file_put_contents($filename, "$data\n", \FILE_APPEND);
    }
}
