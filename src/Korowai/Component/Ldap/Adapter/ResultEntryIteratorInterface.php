<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ResultEntryIteratorInterface.php
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
 * Iterates through entries returned by an ldap search query.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface ResultEntryIteratorInterface extends \Iterator
{
}

// vim: syntax=php sw=4 ts=4 et:
