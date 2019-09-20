<?php
/**
 * @file src/Korowai/Lib/Context/ExecutorInterface.php
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
interface ExecutorInterface
{
    /**
     * Invokes user function.
     *
     * @param callable $func The user function to be called
     * @return mixed The value returned by ``$func``.
     */
    public function __invoke(callable $func);
}

// vim: syntax=php sw=4 ts=4 et:
