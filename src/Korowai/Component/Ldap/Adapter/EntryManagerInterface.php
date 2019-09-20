<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/EntryManagerInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Entry;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface EntryManagerInterface
{
    /**
     * Adds a new entry in the LDAP server.
     *
     * @param Entry $entry
     *
     * @throws LdapException
     */
    public function add(Entry $entry);

    /**
     * Updates an entry in Ldap server
     *
     * @param Entry $entry
     *
     * @throws LdapException
     */
    public function update(Entry $entry);

    /**
     * Renames an entry on the Ldap server
     *
     * @param Entry $entry
     * @param string $newRdn
     * @param bool $deleteOldRdn
     *
     * @throws LdapException
     */
    public function rename(Entry $entry, string $newRdn, bool $deleteOldRdn = true);

    /**
     * Removes an entry from the Ldap server
     *
     * @param Entry $entry
     *
     * @throws LdapException
     */
    public function delete(Entry $entry);
}

// vim: syntax=php sw=4 ts=4 et:
