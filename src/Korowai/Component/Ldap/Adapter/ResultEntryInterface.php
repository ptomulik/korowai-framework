<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ResultEntryInterface.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter;

use Korowai\Component\Ldap\Adapter\ResultAttributeIteratorInterface;
use Korowai\Component\Ldap\Entry;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
interface ResultEntryInterface
{
    /**
     * Returns Distinguished Name (DN) of the result entry
     *
     * @return string Distinguished Name of the result entry
     */
    public function getDn() : string;

    /**
     * Returns entry attributes as an array. The keys in array are lower-case.
     *
     * @return array Entry's attributes
     */
    public function getAttributes() : array;

    /**
     * Creates an ``Entry`` from this object. Equivalent to
     * ``return new Entry($this->getDn(), $this->getAttributes())``.
     *
     * @return Entry A new instance of Entry
     */
    public function toEntry() : Entry;

    /**
     * Returns an iterator over entry's attributes.
     * @return ResultAttributeIteratorInterface Attribute iterator
     */
    public function getAttributeIterator() : ResultAttributeIteratorInterface;
}

// vim: syntax=php sw=4 ts=4 et:
