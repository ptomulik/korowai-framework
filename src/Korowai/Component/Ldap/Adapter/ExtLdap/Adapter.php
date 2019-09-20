<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/Adapter.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\AdapterInterface;
use Korowai\Component\Ldap\Adapter\BindingInterface;
use Korowai\Component\Ldap\Adapter\EntryManagerInterface;
use Korowai\Component\Ldap\Adapter\QueryInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\Binding;
use Korowai\Component\Ldap\Adapter\ExtLdap\EntryManager;
use Korowai\Component\Ldap\Adapter\ExtLdap\Query;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;

use Korowai\Component\Ldap\Adapter\ExtLdap\EnsureLdapLink;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Adapter implements AdapterInterface
{
    private $link;
    private $binding;
    private $entryManager;

    public function __construct(LdapLink $link)
    {
        $this->link = $link;
    }

    /**
     * Returns the ``$link`` provided to ``__construct()`` at creation
     * @return LdapLink The ``$link`` provided to ``__construct()`` at creation
     */
    public function getLdapLink() : LdapLink
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     */
    public function getBinding() : BindingInterface
    {
        if (!isset($this->binding)) {
            $link = $this->getLdapLink();
            $this->binding = new Binding($link);
        }
        return $this->binding;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntryManager() : EntryManagerInterface
    {
        if (!isset($this->entryManager)) {
            $link = $this->getLdapLink();
            $this->entryManager = new EntryManager($link);
        }
        return $this->entryManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery(string $base_dn, string $filter, array $options = array()) : QueryInterface
    {
        return new Query($this->getLdapLink(), $base_dn, $filter, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
