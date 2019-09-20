<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/BindingInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

/**
 * Represents and changes bind state of an ldap link.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface BindingInterface
{
    /**
     * Check whether the connection was already bound or not.
     *
     * @return bool
     */
    public function isBound() : bool;

    /**
     * Binds the connection against a DN and password
     *
     * @param string $dn        The user's DN
     * @param string $password  The associated password
     */
    public function bind(string $dn = null, string $password = null);

    /**
     * Unbinds the connection
     */
    public function unbind();
}

// vim: syntax=php sw=4 ts=4 et:
