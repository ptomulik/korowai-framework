<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/ResultReferenceIterator.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ResultReferenceIteratorInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultReferenceIterator extends AbstractResultIterator implements ResultReferenceIteratorInterface
{
    /**
     * Constructs ResultReferenceIterator
     *
     * @param Result $result                  The ldap search result which provides
     *                                        first entry in the entry chain
     * @param ResultReference|null $reference The current reference in the chain or
     *                                        ``null`` to create an invalid (past the
     *                                        end) iterator
     *
     * The ``$result`` object is used by ``rewind()`` method.
     */
    public function __construct(Result $result, ?ResultReference $reference)
    {
        parent::__construct($result, $reference);
    }

    /**
     * Returns the ``$reference`` provided to ``__construct()`` at creation.
     * @return mixed The ``$reference`` provided to ``__construct()`` at creation.
     */
    public function getReference()
    {
        return $this->getPointed();
    }

    public function getMethodForFirst()
    {
        return 'first_reference';
    }

    protected function getMethodForNext()
    {
        return 'next_reference';
    }
}

// vim: syntax=php sw=4 ts=4 et:
