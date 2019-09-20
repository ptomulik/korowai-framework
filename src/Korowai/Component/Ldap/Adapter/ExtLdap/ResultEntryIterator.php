<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/ResultEntryIterator.php
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
class ResultEntryIterator extends AbstractResultIterator implements ResultEntryIteratorInterface
{
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
    public function __construct(Result $result, ?ResultEntry $entry)
    {
        parent::__construct($result, $entry);
    }

    /**
     * Returns the ``$entry`` provided to ``__construct()`` at creation
     * @return mixed The ``$entry`` provided to ``__construct()`` at creation
     */
    public function getEntry()
    {
        return $this->getPointed();
    }

    protected function getMethodForFirst()
    {
        return 'first_entry';
    }

    protected function getMethodForNext()
    {
        return 'next_entry';
    }
}

// vim: syntax=php sw=4 ts=4 et:
