<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/AdapterInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Adapter\BindingInterface;
use Korowai\Component\Ldap\Adapter\EntryManagerInterface;
use Korowai\Component\Ldap\Adapter\QueryInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides access to an LDAP implementation via set of supplementary
 * interfaces.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface AdapterInterface
{
    /**
     * Returns the current binding object.
     *
     * @return BindingInterface
     */
    public function getBinding() : BindingInterface;
    /**
     * Returns the current entry manager.
     *
     * @return EntryManagerInterface
     */
    public function getEntryManager() : EntryManagerInterface;
    /**
     * Creates a search query.
     *
     * @param string $base_dn Base DN where the search will start
     * @param string $filter Filter used by ldap search
     * @param array $options Additional search options
     *
     * @return QueryInterface
     */
    public function createQuery(string $base_dn, string $filter, array $options = array()) : QueryInterface;
}

// vim: syntax=php sw=4 ts=4 et:
