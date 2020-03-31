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

use PHPUnit\Framework\TestCase;

final class Target
{
    private $className;
    private $methodName;

    public function __construct(string $className, ?string $methodName = null)
    {
        $this->className = $className;
        $this->methodName = $methodName;
    }

    public static function fromTestCase(TestCase $testCase) : self
    {
        return new self(\get_class($testCase), $testCase->getName(false));
    }

    public function getClassName() : string
    {
        return $this->className;
    }

    public function getClassShortName() : string
    {
        return (new \ReflectionClass($this->className))->getShortName();
    }

    public function tryGetMethodName() : ?string
    {
        return $this->methodName;
    }

    public function getMethodName() : string
    {
        if (null === $this->methodName) {
            throw new \LogicException(sprintf('Class level target "%s" does not have method name.', $this->className));
        }

        return $this->methodName;
    }

    public function getMethodShortName() : string
    {
        return preg_replace('/^test(\S+).*$/', '\1', $this->getMethodName());
    }

    public function toString() : string
    {
        return $this->methodName
            ? "$this->className::$this->methodName"
            : $this->className;
    }
}