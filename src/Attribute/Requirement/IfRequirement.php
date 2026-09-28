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

namespace PHPUnitExtras\Attribute\Requirement;

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\RequiresIf;
use PHPUnitExtras\Attribute\Target;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class IfRequirement implements Requirement
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

    #[\Override]
    public function getAttributeClass() : string
    {
        return RequiresIf::class;
    }

    #[\Override]
    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        if (!$attribute instanceof RequiresIf) {
            throw new \InvalidArgumentException('IfRequirement only handles RequiresIf attributes');
        }

        $expression = $placeholderResolver->resolve($attribute->expression, $target);

        if ($this->language->evaluate($expression, $this->context)) {
            return null;
        }

        return \sprintf('"%s" is not evaluated to true', $expression);
    }

    /**
     * Returns null for missing keys when expressions access PHP superglobals.
     *
     * @param array<array-key, mixed> $data
     * @return \ArrayObject<array-key, mixed>
     */
    private static function wrapGlobal(array $data) : \ArrayObject
    {
        /** @psalm-suppress MissingTemplateParam */
        return new class($data) extends \ArrayObject {
            public function __get(string $key) : mixed
            {
                return $this->offsetExists($key) ? $this->offsetGet($key) : null;
            }
        };
    }
}
