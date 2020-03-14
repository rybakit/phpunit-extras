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

namespace PHPUnitExtras\Annotation\PlaceholderResolver;

use PHPUnitExtras\Annotation\Target;

final class TmpDirResolver implements PlaceholderResolver
{
    public function getName() : string
    {
        return 'tmp_dir';
    }

    public function resolve(string $value, Target $target) : string
    {
        return strtr($value, ['%tmp_dir%' => sys_get_temp_dir()]);
    }
}
