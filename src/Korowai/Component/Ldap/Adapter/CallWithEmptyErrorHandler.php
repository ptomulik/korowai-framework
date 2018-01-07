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
trait CallWithEmptyErrorHandler
{
    private static function emptyErrorHandler_b7830210430($errno, $errstr)
    {
        return true;
    }

    /**
     * Temporarily disables error handler by setting it to an empty function
     * and invokes user provided method
     *
     * @param string $name Method name
     * @param mixed $args Arguments
     * @return mixed Whatever the called method returns
     */
    public function callWithEmptyErrorHandler(string $name, ...$args)
    {
        set_error_handler([static::class, 'emptyErrorHandler_b7830210430']);
        try {
            $retval = call_user_func_array([$this,$name], $args);
        } finally {
            restore_error_handler();
        }
        return $retval;
    }
}

// vim: syntax=php sw=4 ts=4 et:
