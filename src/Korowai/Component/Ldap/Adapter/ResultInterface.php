<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ResultInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Adapter\ResultEntryIteratorInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface ResultInterface extends \IteratorAggregate
{
    /**
     * Get iterator over result's entries
     *
     * @return ResultEntryIteratorInterface The iterator
     */
    public function getResultEntryIterator() : ResultEntryIteratorInterface;

    /**
     * Get iterator over result's references
     *
     * @return ResultReferenceIteratorInterface The iterator
     */
    public function getResultReferenceIterator() : ResultReferenceIteratorInterface;

    /**
     * Get an array of Entries from ldap result
     *
     * @return array Entries
     */
    public function getEntries(bool $use_keys = true) : array;
}

// vim: syntax=php sw=4 ts=4 et:
