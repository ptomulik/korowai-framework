<?php
/**
 * This file is part of the Korowai package
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);
namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\BindingInterface;
use Korowai\Component\Ldap\Exception\LdapException;
use Korowai\Component\Ldap\Adapter\CallWithEmptyErrorHandler;

use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\LastLdapException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Binding implements BindingInterface
{
    use CallWithEmptyErrorHandler;
    use LastLdapException;

    /** @var bool */
    private $bound = false;

    /** @var resource */
    private $link;

    /**
     * Initializes the Binding object with LdapLink instance.
     *
     * @param LdapLink $link
     */
    public function __construct(LdapLink $link)
    {
        $this->link = $link;
    }

    /**
     * Returns a link resource.
     *
     * @return resource
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Same as getLink()->isValid();
     */
    public function isLinkValid()
    {
        return $this->link->isValid();
    }

    /**
     * Ensures that the link is initialised. If not, throws an exception.
     *
     * @throws LdapException
     *
     * @internal
     * @return bool Always returns true.
     */
    public function ensureLink() : bool
    {
        if(!$this->isLinkValid()) {
            throw new LdapException("Uninitialized LDAP link", -1);
        }
        return true;
    }

    /**
     * If the link is valid, returns last error code related to link.
     * Otherwise, returns -1.
     *
     * @return int
     */
    public function errno()
    {
        return $this->isLinkValid() ? $this->link->errno() : -1;
    }

    /**
     * {@inheritdoc}
     */
    public function isBound() : bool
    {
        return $this->bound;
    }

    /**
     * {@inheritdoc}
     */
    public function bind(string $dn = null, string $password = null)
    {
        return $this->callImplMethod('bindImpl', $dn, $password);
    }

    /**
     * Get LDAP option's value (as per ldap_get_option())
     *
     * @param int $option Option identifier (name)
     * @return mixed Option value
     * @throws LdapException
     */
    public function getOption(int $option)
    {
        return $this->callImplMethod('getOptionImpl', $option);
    }

    /**
     * Set value to LDAP option
     *
     * @param int $option Option identifier (name)
     * @param mixed $value New value
     * @throws LdapException
     */
    public function setOption(int $option, $value)
    {
        return $this->callImplMethod('setOptionImpl', $option, $value);
    }

    /**
     * Unbinds the link
     *
     * After unbind the connection is no longer valid (and useful)
     *
     * @throws LdapException
     */
    public function unbind()
    {
        return $this->callImplMethod('unbindImpl');
    }

    /**
     * @internal
     */
    private function callImplMethod($name, ...$args)
    {
        $this->ensureLink();
        return $this->callWithEmptyErrorHandler($name, ...$args);
    }

    /**
     * @internal
     */
    private function bindImpl(string $dn = null, string $password = null)
    {
        $result = $this->getLink()->bind($dn, $password);
        if(!$result) {
            $this->bound = false;
            throw static::lastLdapException($this->link);
        }
        $this->bound = true;
        return $result;
    }

    /**
     * @internal
     */
    private function getOptionImpl(int $option)
    {
        if(!$this->link->get_option($option, $retval)) {
            throw static::lastLdapException($this->link);
        }
        return $retval;
    }

    /**
     * @internal
     */
    public function setOptionImpl(int $option, $value)
    {
        if(!$this->link->set_option($option, $value)) {
            throw static::lastLdapException($this->link);
        }
    }

    /**
     * @internal
     */
    private function unbindImpl()
    {
        if(!$this->link->unbind()) {
            throw static::lastLdapException($this->link);
        }
        $this->bound = false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
