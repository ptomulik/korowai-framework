<?php
/**
 * @file src/Korowai/Lib/Context/ContextFactoryStackInterface.php
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
 * A stack of instances implementing ContextFactoryInterface.
 */
interface ContextFactoryStackInterface
{
    /**
     * Resets the stack to empty state.
     */
    public function clean();

    /**
     * Returns the factory from the top of stack or null if the stack is empty.
     */
    public function top() : ?ContextFactoryInterface;

    /**
     * Pushes the $factory to the top of stack.
     */
    public function push(ContextFactoryInterface $factory);

    /**
     * Pops and returns the factory from the top of stack shortening the array
     * of factories by one element.
     *
     * If the stack is empty, returns null
     *
     * @return ContextFactoryInterface|null
     */
    public function pop() : ?ContextFactoryInterface;


    /**
     * Returns the stack size.
     *
     * @return int
     */
    public function size() : int;
}

// vim: syntax=php sw=4 ts=4 et:
