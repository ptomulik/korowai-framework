<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ResultReferenceInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface ResultReferenceInterface
{
    /**
     * Returns referrals
     * @return array An array of referrals
     */
    public function getReferrals();
}

// vim: syntax=php sw=4 ts=4 et:
