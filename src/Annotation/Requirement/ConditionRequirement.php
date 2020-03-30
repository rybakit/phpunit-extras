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
        $this->language = $language ?? new ExpressionLanguage(null, [new ConditionExpressionLanguageProvider()]);
    }

    public static function fromGlobals() : self
    {
        return new self([
            'cookie' => new \ArrayObject($_COOKIE, \ArrayObject::ARRAY_AS_PROPS),
            'env' => new \ArrayObject($_ENV, \ArrayObject::ARRAY_AS_PROPS),
            'get' => new \ArrayObject($_GET, \ArrayObject::ARRAY_AS_PROPS),
            'files' => new \ArrayObject($_FILES, \ArrayObject::ARRAY_AS_PROPS),
            'post' => new \ArrayObject($_POST, \ArrayObject::ARRAY_AS_PROPS),
            'request' => new \ArrayObject($_REQUEST, \ArrayObject::ARRAY_AS_PROPS),
            'server' => new \ArrayObject($_SERVER, \ArrayObject::ARRAY_AS_PROPS),
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
}
