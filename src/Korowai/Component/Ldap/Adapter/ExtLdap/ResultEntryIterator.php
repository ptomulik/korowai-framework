<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ResultEntryIteratorInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultEntryIterator implements ResultEntryIteratorInterface
{
    /** @var Result */
    private $result;
    /** @var ResultEntry */
    private $entry;

    /**
     * Constructs ResultEntryIterator
     *
     * @param Result $result            The ldap search result which provides
     *                                  first entry in the entry chain
     * @param ResultEntry|null $entry   The current entry in the chain or
     *                                  ``null`` to create an invalid (past the
     *                                  end) iterator
     *
     * The ``$result`` object is used by ``rewind()`` method.
     */
    public function __construct(Result $result, $entry)
    {
        $this->result = $result;
        $this->entry = $entry;
    }

    /**
     * Returns the ``$result`` provided to ``__construct()`` when the object
     * was created.
     *
     * @return Result The result object provided as ``$result`` argument to
     *         ``__construct()``.
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Returns the ``$entry`` provided to ``__construct()`` at creation
     * @return mixed The ``$entry`` provided to ``__construct()`` at creation
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Return the current element, that is the current entry
     */
    public function current()
    {
        return $this->entry;
    }

    /**
     * Return the key of the current element, that is DN of the current entry
     */
    public function key()
    {
        return $this->entry->getDn();
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $this->entry = $this->entry->next_entry();
    }

    /**
     * Rewind the iterator to the first element
     */
    public function rewind()
    {
        $this->entry = $this->result->first_entry();
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return ($this->entry instanceof ResultEntry);
    }
}

// vim: syntax=php sw=4 ts=4 et:
