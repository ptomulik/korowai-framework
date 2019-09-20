<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/LdapLinkOptions.php
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
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait LdapLinkOptions
{
    private static $ldapLinkOptionDeclarations = array(
        'deref'               => array('types' => 'int',    'constant' => 'LDAP_OPT_DEREF'),
        'sizelimit'           => array('types' => 'int',    'constant' => 'LDAP_OPT_SIZELIMIT'),
        'timelimit'           => array('types' => 'int',    'constant' => 'LDAP_OPT_TIMELIMIT'),
        'network_timeout'     => array('types' => 'int',    'constant' => 'LDAP_OPT_NETWORK_TIMEOUT'),
        'protocol_version'    => array('types' => 'int',    'constant' => 'LDAP_OPT_PROTOCOL_VERSION', 'default' => 3),
        'error_number'        => array('types' => 'int',    'constant' => 'LDAP_OPT_ERROR_NUMBER'),
        'referrals'           => array('types' => 'bool',   'constant' => 'LDAP_OPT_REFERRALS'),
        'restart'             => array('types' => 'bool',   'constant' => 'LDAP_OPT_RESTART'),
        'host_name'           => array('types' => 'string', 'constant' => 'LDAP_OPT_HOST_NAME'),
        'error_string'        => array('types' => 'string', 'constant' => 'LDAP_OPT_ERROR_STRING'),
        'diagnostic_message'  => array('types' => 'string', 'constant' => 'LDAP_OPT_DIAGNOSTIC_MESSAGE'),
        'matched_dn'          => array('types' => 'string', 'constant' => 'LDAP_OPT_MATCHED_DN'),
        'server_controls'     => array('types' => 'array',  'constant' => 'LDAP_OPT_SERVER_CONTROLS'),
        'client_controls'     => array('types' => 'array',  'constant' => 'LDAP_OPT_CLIENT_CONTROLS'),

        'keepalive_idle'      => array('types' => 'int',    'constant' => 'LDAP_OPT_X_KEEPALIVE_IDLE'),
        'keepalive_probes'    => array('types' => 'int',    'constant' => 'LDAP_OPT_X_KEEPALIVE_PROBES'),
        'keepalive_interval'  => array('types' => 'int',    'constant' => 'LDAP_OPT_X_KEEPALIVE_INTERVAL'),

        'sasl_mech'           => array('types' => 'string', 'constant' => 'LDAP_OPT_X_SASL_MECH'),
        'sasl_realm'          => array('types' => 'string', 'constant' => 'LDAP_OPT_X_SASL_REALM'),
        'sasl_authcid'        => array('types' => 'string', 'constant' => 'LDAP_OPT_X_SASL_AUTHCID'),
        'sasl_authzid'        => array('types' => 'string', 'constant' => 'LDAP_OPT_X_SASL_AUTHZID'),
        // PHP >= 7.1.0
        'tls_cacertdir'       => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_CACERTDIR'),
        'tls_cacertfile'      => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_CACERTFILE'),
        'tls_certfile'        => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_CERTFILE'),
        'tls_cipher_suite'    => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_CIPHER_SUITE'),
        'tls_crlcheck'        => array('types' => 'int',    'constant' => 'LDAP_OPT_X_TLS_CRLCHECK'),
        'tls_crlfile'         => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_CRLFILE'),
        'tls_dhfile'          => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_DHFILE'),
        'tls_keyfile'         => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_KEYFILE'),
        'tls_protocol_min'    => array('types' => 'int',    'constant' => 'LDAP_OPT_X_TLS_PROTOCOL_MIN'),
        'tls_random_file'     => array('types' => 'string', 'constant' => 'LDAP_OPT_X_TLS_RANDOM_FILE'),
        // PHP >= 7.0.5
        'tls_require_cert'    => array('types' => 'int',    'constant' => 'LDAP_OPT_X_TLS_REQUIRE_CERT'),
    );

    /**
     * Returns name of an ext-ldap option constant for a given option name
     * @return string Name of the ext-ldap constant
     */
    public function getLdapLinkOptionConstantName($optionName)
    {
        if (!isset(self::$ldapLinkOptionDeclarations[$optionName]['constant'])) {
            return null;
        }
        $name = self::$ldapLinkOptionDeclarations[$optionName]['constant'];
        return defined($name) ? $name : null;
    }

    /**
     * Returns value of an ext-ldap option constant for a given option name
     *
     * @throws LdapException
     * @return mixed Value of the ext-ldap constant
     */
    public function getLdapLinkOptionConstant($name)
    {
        $constantName = $this->getLdapLinkOptionConstantName($name);

        if (!$constantName) {
            throw new LdapException("Unknown option '$name'", -1);
        }

        return constant($constantName);
    }

    /**
     * Returns declarations of options, mainly for use by ``configureLdapLinkOptions()``
     * @return array Declarations
     */
    public function getLdapLinkOptionDeclarations()
    {
        static $existingOptions;
        if (!isset($existingOptions)) {
            $existingOptions = array_filter(
                self::$ldapLinkOptionDeclarations,
                [$this, 'getLdapLinkOptionConstantName'],
                ARRAY_FILTER_USE_KEY
            );
        }
        return $existingOptions;
    }

    /**
     * Configures symfony's  OptionsResolver to parse LdapLink options
     */
    protected function configureLdapLinkOptions(OptionsResolver $resolver)
    {
        $ldapLinkOptionDeclarations = $this->getLdapLinkOptionDeclarations();
        foreach ($ldapLinkOptionDeclarations as $name => $option) {
            if (array_key_exists('default', $option)) {
                $resolver->setDefault($name, $option['default']);
            } else {
                $resolver->setDefined($name);
            }
            $resolver->setAllowedTypes($name, $option['types']);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
