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

namespace PHPUnitExtras\Annotation\Requirement;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConditionRequirement implements Requirement
{
    /** @var array<array-key, mixed> */
    private $context;

    /** @var ExpressionLanguage */
    private $language;

    public function __construct(array $context, ?ExpressionLanguage $language = null)
    {
        $this->context = $context;
        $this->language = $language ?? new ExpressionLanguage(null, [new ConditionFunctionProvider()]);
    }

    public static function fromGlobals() : self
    {
        return new self([
            'cookie' => self::wrapGlobal($_COOKIE),
            'env' => self::wrapGlobal($_ENV),
            'get' => self::wrapGlobal($_GET),
            'files' => self::wrapGlobal($_FILES),
            'post' => self::wrapGlobal($_POST),
            'request' => self::wrapGlobal($_REQUEST),
            'server' => self::wrapGlobal($_SERVER),
        ]);
    }

    public function getName() : string
    {
        return 'condition';
    }

    public function check(string $value) : ?string
    {
        if ($this->language->evaluate($value, $this->context)) {
            return null;
        }

        return sprintf('"%s" is not evaluated to true', $value);
    }

    /**
     * A workaround for unsupported "nullsafe" and "null coalescing" operators.
     * @see https://github.com/symfony/symfony/issues/21691
     */
    private static function wrapGlobal(array $data) : \ArrayObject
    {
        return new class ($data) extends \ArrayObject {
            public function __get($key) {
                return $this->offsetExists($key) ? $this->offsetGet($key) : null;
            }
        };
    }
}
