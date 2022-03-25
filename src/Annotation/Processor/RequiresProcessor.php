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

namespace PHPUnitExtras\Annotation\Processor;

use PHPUnit\Framework\Assert;
use PHPUnitExtras\Annotation\InvalidAnnotationException;
use PHPUnitExtras\Annotation\Requirement\Requirement;

final class RequiresProcessor implements Processor
{
    /**
     * @see https://github.com/sebastianbergmann/phpunit/blob/7.1.0/src/Util/Test.php#L67-L90
     */
    private const PHPUNIT_REQUIREMENTS = [
        'PHP' => true,
        'PHPUnit' => true,
        'OS' => true,
        'OSFAMILY' => true,
        'function' => true,
        'extension' => true,
        'setting' => true,
    ];

    /** @var array<string, Requirement> */
    private $requirements = [];

    /**
     * @param array<array-key, Requirement> $requirements
     */
    public function __construct(array $requirements)
    {
        foreach ($requirements as $requirement) {
            $this->addRequirement($requirement);
        }
    }

    /**
     * @return array<string, Requirement>
     */
    public function getRequirements() : array
    {
        return $this->requirements;
    }

    public function getName() : string
    {
        return 'requires';
    }

    public function process(string $value) : void
    {
        [$reqName, $reqValue] = explode(' ', $value, 2) + [1 => ''];

        $found = false;
        foreach ($this->requirements as $name => $requirement) {
            if ($name !== $reqName) {
                continue;
            }

            $found = true;
            if (null === $error = $requirement->check($reqValue)) {
                continue;
            }

            Assert::markTestSkipped($error);
        }

        if (!$found && !isset(self::PHPUNIT_REQUIREMENTS[$reqName])) {
            throw InvalidAnnotationException::unknownRequirement($reqName);
        }
    }

    private function addRequirement(Requirement $requirement) : void
    {
        $this->requirements[$requirement->getName()] = $requirement;
    }
}
