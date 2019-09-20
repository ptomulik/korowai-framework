<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/EnsureLdapLink.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Exception\LdapException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait EnsureLdapLink
{
    /**
     * Ensures that the link is initialised. If not, throws an exception.
     *
     * @param LdapLink $link
     * @throws LdapException
     *
     * @return bool Always returns true.
     */
    protected static function ensureLdapLink(LdapLink $link) : bool
    {
        if (!$link->isValid()) {
            throw new LdapException("Uninitialized LDAP link", -1);
        }
        return true;
    }
}

// vim: syntax=php sw=4 ts=4 et:
