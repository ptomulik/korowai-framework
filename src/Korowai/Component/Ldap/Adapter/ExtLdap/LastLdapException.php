<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/LastLdapException.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Exception\LdapException;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait LastLdapException
{
    protected static function lastLdapException(LdapLink $link)
    {
        $errno = $link->errno();
        $errstr = LdapLink::err2str($errno);
        return new LdapException($errstr, $errno);
    }
}

// vim: syntax=php sw=4 ts=4 et:
