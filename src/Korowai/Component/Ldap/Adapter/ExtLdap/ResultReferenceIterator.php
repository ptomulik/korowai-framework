<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ResultReferenceIteratorInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultReferenceIterator implements ResultReferenceIteratorInterface
{
    private $result;
    private $reference;

    public function __construct(Result $result, $reference)
    {
        $this->result = $result;
        $this->reference = $reference;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Return the current element, that is the current reference
     */
    public function current()
    {
        return $this->reference;
    }

    /**
     * Return the key of the current element, that is DN of the current reference
     */
    public function key()
    {
        return $this->reference->getDn();
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $this->reference = $this->reference->next_reference();
    }

    /**
     * Rewind the iterator to the first element
     */
    public function rewind()
    {
        $this->reference = $this->result->first_reference();
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return ($this->reference instanceof ResultReference);
    }
}

// vim: syntax=php sw=4 ts=4 et:
