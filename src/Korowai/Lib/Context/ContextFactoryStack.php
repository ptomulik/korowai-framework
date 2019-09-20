<?php
/**
 * @file src/Korowai/Lib/Context/ContextFactoryStack.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Contextlib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context;

/**
 * A composite context factory which collects other factories and organizes
 * them into a stack.
 *
 * All factories from the stack get requested by getContextManger() to create a
 * context manager, starting from the top and walking down. The first factory,
 * which returns non-null, wins.
 */
class ContextFactoryStack implements ContextFactoryStackInterface, ContextFactoryInterface
{
    use \Korowai\Lib\Basic\Singleton;

    /**
     * @var ContextFactoryInterface[]
     */
    protected $factories;

    /**
     * Initializes the object.
     */
    protected function initializeSingleton()
    {
        $this->clean();
    }

    /**
     * {@inheritdoc}
     */
    public function clean()
    {
        $this->factories = [];
    }

    /**
     * {@inheritdoc}
     */
    public function top() : ?ContextFactoryInterface
    {
        return array_slice($this->factories, -1)[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function push(ContextFactoryInterface $factory)
    {
        return array_push($this->factories, $factory);
    }

    /**
     * Pops and returns the factory from the top of stack shortening the array
     * of factories by one element.
     *
     * If the stack is empty, returns null
     *
     * @return ContextFactoryInterface|null
     */
    public function pop() : ?ContextFactoryInterface
    {
        return array_pop($this->factories);
    }

    /**
     * {@inheritdoc}
     */
    public function size() : int
    {
        return count($this->factories);
    }

    /**
     * {@inheritdoc}
     */
    public function getContextManager($arg) : ?ContextManagerInterface
    {
        for ($i = count($this->factories) - 1; $i >= 0; $i--) {
            $factory = $this->factories[$i];
            if (null !== ($cm = $factory->getContextManager($arg))) {
                return $cm;
            }
        }
        return null;
    }
}

// vim: syntax=php sw=4 ts=4 et:
