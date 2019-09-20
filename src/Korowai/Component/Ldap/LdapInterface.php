<?php
/**
 * @file src/Korowai/Component/Ldap/LdapInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap;

use Korowai\Component\Ldap\Adapter\BindingInterface;
use Korowai\Component\Ldap\Adapter\EntryManagerInterface;
use Korowai\Component\Ldap\Adapter\AdapterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LDAP interface
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface LdapInterface extends
    BindingInterface,
    EntryManagerInterface,
    AdapterInterface
{
    /**
     * Returns adapter
     * @return AdapterInterface Adapter
     */
    public function getAdapter() : AdapterInterface;
}

// vim: syntax=php sw=4 ts=4 et:
