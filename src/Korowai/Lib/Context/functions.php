<?php
/**
 * @file src/Korowai/Lib/Context/functions.php
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
 * Turns $arg into a context manager.
 *
 * @param mixed $arg
 * @return ContextManagerInterface
 */
function get_context_manager($arg) : ContextManagerInterface
{
    return ContextFactoryStack::getInstance()->getContextManager($arg) ??
           DefaultContextFactory::getInstance()->getContextManager($arg);
}

/**
 * Creates an executor object which invokes user function within a context.
 *
 * @access public
 * @return ExecutorInterface
 */
function with(... $args) : ExecutorInterface
{
    $context = array_map(get_context_manager::class, $args);
    return new WithContextExecutor($context);
}

// vim: syntax=php sw=4 ts=4 et:
