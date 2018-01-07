<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait CallWithCustomErrorHandler
{
    /**
     * Temporarily sets custom error handler and invokes user provided method
     *
     * @param string $name Method name
     * @param mixed $args Arguments
     * @return mixed Whatever the called method returns
     */
    public function callWithCustomErrorHandler(callable $handler, string $name, ...$args)
    {
        set_error_handler($handler);
        try {
            $retval = call_user_func_array([$this,$name], $args);
        } finally {
            restore_error_handler();
        }
        return $retval;
    }
}

// vim: syntax=php sw=4 ts=4 et:
