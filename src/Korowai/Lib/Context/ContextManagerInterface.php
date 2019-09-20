<?php
/**
 * @file src/Korowai/Lib/Context/ContextManagerInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ContextLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context;

/**
 * Interface for context managers.
 */
interface ContextManagerInterface
{
    /**
     * Enter the runtime context and return either this object or another
     * object related to the runtime context.
     *
     * The value returned by this method is bound to a positional argument
     * of the user function that is going to be called within the context.
     *
     * @return mixed
     */
    public function enterContext();

    /**
     * Exit the runtime context and return a Boolean flag indicating if any
     * exception that occured should be suppressed.
     *
     * If an exception occurred while executing the user function, the
     * ``$exception`` argument contain the exception. Otherwise it contains
     * ``null``.
     *
     * Returning a true from this method will cause the "with" caller to
     * suppress the exception and continue execution.
     *
     * @param \Throwable $exception The exception thrown from user function or
     *                              ``null``.
     * @return bool
     */
    public function exitContext(?\Throwable $exception = null) : bool;
}

// vim: syntax=php sw=4 ts=4 et:
