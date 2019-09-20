<?php
/**
 * @file src/Korowai/Lib/Error/ErrorHandlerInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ErrorLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Error;

/**
 * An interface for error handler objects.
 */
interface ErrorHandlerInterface
{
    /**
     * Actual error handler function.
     *
     * @param int $severity Level of the error raised.
     * @param string $message Error message.
     * @param string $file File name the the error was raised in.
     * @param int $line Line number the error was raised at.
     *
     *
     * @return bool If it returns ``false``, then the normal error handler
     *              continues.
     */
    public function __invoke(int $severity, string $message, string $file, int $line) : bool;
}

// vim: syntax=php sw=4 ts=4 et:
