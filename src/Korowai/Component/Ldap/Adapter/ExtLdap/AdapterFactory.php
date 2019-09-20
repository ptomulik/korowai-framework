<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/AdapterFactory.php
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
use Korowai\Component\Ldap\Adapter\AbstractAdapterFactory;
use Korowai\Component\Ldap\Exception\LdapException;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\Adapter;

use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLinkOptions;
use Korowai\Component\Ldap\Adapter\ExtLdap\EnsureLdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\LastLdapException;

use function Korowai\Lib\Context\with;
use Korowai\Lib\Error\ExceptionErrorHandler;
use Korowai\Lib\Error\EmptyErrorHandler;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AdapterFactory extends AbstractAdapterFactory
{
    use LdapLinkOptions;
    use EnsureLdapLink;
    use LastLdapException;

    /**
     * Creates instance of AdapterFactory
     *
     * @throws LdapException
     */
    public function __construct(array $config = null)
    {
        if (!@extension_loaded('ldap')) {
            throw new LdapException("The LDAP PHP extension is not enabled.", -1);
        }
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureNestedOptionsResolver(OptionsResolver $resolver)
    {
        $this->configureLdapLinkOptions($resolver);
    }

    private function createLdapLink()
    {
        $handler = ExceptionErrorHandler::create(function ($severity, $message, ...$args) {
            return new LdapException($message, -1, $severity, ...$args);
        });
        $link = with($handler)(function ($eh) {
            return $this->createLdapLinkImpl();
        });
        if (!$link) {
            // throw this exception in case ldap-ext forgot to trigger_error
            throw new LdapException('Failed to create LDAP connection', -1);
        }
        return $link;
    }

    private function createLdapLinkImpl()
    {
        $config = $this->getConfig();
        return LdapLink::connect($config['uri']);
    }

    private function configureLdapLink(LdapLink $link)
    {
        $config = $this->getConfig();
        foreach ($config['options'] as $name => $value) {
            $option = $this->getLdapLinkOptionConstant($name);
            $this->setLdapLinkOption($link, $option, $value);
        }
    }

    private function setLdapLinkOption(LdapLink $link, int $option, $value)
    {
        static::ensureLdapLink($link);
        with(EmptyErrorHandler::getInstance())(function ($eh) use ($link, $option, $value) {
            $this->setLdapLinkOptionImpl($link, $option, $value);
        });
    }

    private function setLdapLinkOptionImpl(LdapLink $link, int $option, $value)
    {
        if (!$link->set_option($option, $value)) {
            throw static::lastLdapException($link);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createAdapter() : AdapterInterface
    {
        $link = $this->createLdapLink();
        $this->configureLdapLink($link);
        return new Adapter($link);
    }
}

// vim: syntax=php sw=4 ts=4 et:
