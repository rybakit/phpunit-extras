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

use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\Target;

final class ChainResolver implements PlaceholderResolver
{
    /** @var array<string, PlaceholderResolver> */
    private $resolvers = [];

    public function __construct(array $placeholders = [])
    {
        foreach ($placeholders as $placeholder) {
            $this->addResolver($placeholder);
        }
    }

    public function addResolver(PlaceholderResolver $resolver) : self
    {
        $this->resolvers[$resolver->getName()] = $resolver;

        return $this;
    }

    public function getName() : string
    {
        return 'chain';
    }

    public function resolve(string $value, Target $target) : string
    {
        foreach ($this->resolvers as $resolver) {
            $value = $resolver->resolve($value, $target);
        }

        if (preg_match('/%(?P<placeholder>[^%]+)%/', $value, $matches)) {
            throw InvalidAnnotationException::unresolvedPlaceholder($matches['placeholder']);
        }

        return $value;
    }
}
