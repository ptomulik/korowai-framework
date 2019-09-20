<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/EntryManager.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\EntryManagerInterface;
use Korowai\Component\Ldap\Entry;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;

use Korowai\Component\Ldap\Adapter\ExtLdap\EnsureLdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\LastLdapException;

use function Korowai\Lib\Context\with;
use Korowai\Lib\Error\EmptyErrorHandler;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class EntryManager implements EntryManagerInterface
{
    use EnsureLdapLink;
    use LastLdapException;

    private $link;

    /**
     * Constructs EntryManager
     */
    public function __construct(LdapLink $link)
    {
        $this->link = $link;
    }

    /**
     * Returns a link resource
     *
     * @return resource
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_add().
     */
    public function add(Entry $entry)
    {
        return $this->callImplMethod('addImpl', $entry);
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_modify()
     */
    public function update(Entry $entry)
    {
        return $this->callImplMethod('updateImpl', $entry);
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_rename()
     */
    public function rename(Entry $entry, string $newRdn, bool $deleteOldRdn = true)
    {
        return $this->callImplMethod('renameImpl', $entry, $newRdn, $deleteOldRdn);
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_delete()
     */
    public function delete(Entry $entry)
    {
        return $this->callImplMethod('deleteImpl', $entry);
    }

    /**
     * @internal
     */
    private function callImplMethod($name, ...$args)
    {
        static::ensureLdapLink($this->link);
        return with(EmptyErrorHandler::getInstance())(function ($eh) use ($name, $args) {
            return call_user_func_array([$this, $name], $args);
        });
    }

    /**
     * @internal
     */
    private function addImpl(Entry $entry)
    {
        if (!$this->getLink()->add($entry->getDn(), $entry->getAttributes())) {
            throw static::lastLdapException($this->link);
        }
    }

    /**
     * @internal
     */
    public function updateImpl(Entry $entry)
    {
        if (!$this->getLink()->modify($entry->getDn(), $entry->getAttributes())) {
            throw static::lastLdapException($this->link);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_rename()
     */
    public function renameImpl(Entry $entry, string $newRdn, bool $deleteOldRdn = true)
    {
        if (!$this->getLink()->rename($entry->getDn(), $newRdn, null, $deleteOldRdn)) {
            throw static::lastLdapException($this->link);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Invokes ldap_delete()
     */
    public function deleteImpl(Entry $entry)
    {
        if (!$this->getLink()->delete($entry->getDn())) {
            throw static::lastLdapException($this->link);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
