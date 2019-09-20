<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/AbstractResultIterator.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ResultEntryIteratorInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
abstract class AbstractResultIterator
{
    /** @var Result */
    private $result;
    private $pointed;

    /**
     * Constructs ResultEntryIterator
     *
     * @param Result $result        The ldap search result which provides first entry in the entry chain
     * @param object|null $pointed  The element currently pointed to by iterator.
     *
     * The ``$result`` object is used by ``rewind()`` method.
     */
    public function __construct(Result $result, $pointed)
    {
        $this->result = $result;
        $this->pointed = $pointed;
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
     * Returns the ``$pointed`` provided to ``__construct()`` at creation
     * @return mixed The ``$pointed`` provided to ``__construct()`` at creation
     */
    public function getPointed()
    {
        return $this->pointed;
    }

    /**
     * Return the current element, that is the current entry
     */
    public function current()
    {
        return $this->pointed;
    }

    /**
     * Return the key of the current element, that is DN of the current entry
     */
    public function key()
    {
        return $this->pointed->getDn();
    }

    /**
     * Move forward to next element
     */
    public function next()
    {
        $method = $this->getMethodForNext();
        $this->pointed = call_user_func([$this->pointed, $method]);
    }

    /**
     * Rewind the iterator to the first element
     */
    public function rewind()
    {
        $method = $this->getMethodForFirst();
        $this->pointed = call_user_func([$this->result, $method]);
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return is_object($this->pointed);
    }

    abstract protected function getMethodForNext();
    abstract protected function getMethodForFirst();
}

// vim: syntax=php sw=4 ts=4 et:
