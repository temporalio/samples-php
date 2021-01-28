<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Temporal\SampleUtils;

use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class DeclarationLocator
{
    private ClassesInterface $classLocator;

    /**
     * @return  \Generator
     */
    public function getCommands(): \Generator
    {
        foreach ($this->classLocator->getClasses(Command::class) as $class) {
            if (!$class->isAbstract()) {
                yield $class->getName();
            }
        }
    }

    /**
     * Finds all activity declarations using Activity suffix.
     *
     * @return  \Generator
     */
    public function getActivityTypes(): \Generator
    {
        foreach ($this->getAvailableDeclarations() as $class) {
            if ($this->endsWith($class->getName(), 'Activity')) {
                yield $class->getName();
            }
        }
    }

    /**
     * Finds all workflow declarations using Workflow suffix.
     *
     * @return  \Generator
     */
    public function getWorkflowTypes(): \Generator
    {
        foreach ($this->getAvailableDeclarations() as $class) {
            if ($this->endsWith($class->getName(), 'Workflow')) {
                yield $class->getName();
            }
        }
    }

    /**
     * @return \Generator|\ReflectionClass[]
     */
    private function getAvailableDeclarations(): \Generator
    {
        foreach ($this->classLocator->getClasses() as $class) {
            if ($class->isAbstract() || $class->isInterface()) {
                continue;
            }

            yield $class;
        }
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    private function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if (!$length) {
            return true;
        }
        return substr($haystack, -$length) === $needle;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public static function create(string $dir): self
    {
        $locator = new self();
        $locator->classLocator = new ClassLocator(
            Finder::create()->files()->in($dir)
        );

        return $locator;
    }


}