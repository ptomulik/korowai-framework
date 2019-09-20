<?php
/**
 * @file src/Korowai/Component/Ldap/AbstractLdap.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap;

use Korowai\Component\Ldap\LdapInterface;
use Korowai\Component\Ldap\Adapter\ResultInterface;

use \InvalidArgumentException;

/**
 * An abstract base for Ldap class.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class AbstractLdap implements LdapInterface
{
    /**
     * Create query, execute and return its result
     *
     * @param string $base_dn
     * @param string $filter
     * @param array $options
     *
     * @return ResultInterface Query result
     */
    public function query(string $base_dn, string $filter, array $options = array()) : ResultInterface
    {
        return $this->createQuery($base_dn, $filter, $options)->getResult();
    }
}

// vim: syntax=php sw=4 ts=4 et:
