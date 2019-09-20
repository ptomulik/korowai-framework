<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/ResultAttributeIterator.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ResultAttributeIteratorInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;

/**
 * Iterates through an ldap result entry attributes.
 *
 * Only one instance of ``ResultAttributeIterator`` should be used for a given
 * ``ResultEntry``. The internal state (position) of the iterator is
 * keept and managed by the ``"ldap entry"`` resource (encapsulated by our
 * ``ResultEntry`` object which is provided as ``$entry`` argument to
 * ``ResultAttributeIterator::__construct()``). This is a consequence of how
 * PHP ldap extension implements attribute iteration &mdash; the ``berptr``
 * argument to ``libldap`` functions
 * [ldap_first_attribute (3)](https://manpages.debian.org/stretch-backports/libldap2-dev/ldap_first_attribute.3.en.html)
 * and
 * [ldap_next_attribute (3)](https://manpages.debian.org/stretch-backports/libldap2-dev/ldap_next_attribute.3.en.html)
 * is stored by PHP ldap extension in an ``"ldap entry"`` resource and is
 * inaccessible for user.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultAttributeIterator implements ResultAttributeIteratorInterface
{
    /** @var ResultEntry */
    private $entry;

    /** @var string */
    private $attribute;

    /**
     * Initializes the ``ResultAttributeIterator``.
     *
     * The ``$attribute`` should be a valid attribute name returned by either
     * ``$entry->first_attribute()`` or ``$entry->next_attribute()`` or
     * it should be null.
     *
     * @param ResultEntry $entry An ldap entry containing the attributes
     * @param string|null $attribute Name of the current attribute pointed to by Iterator
     */
    public function __construct(ResultEntry $entry, $attribute)
    {
        $this->entry = $entry;
        $this->attribute = is_string($attribute) ? strtolower($attribute) : $attribute;
    }

    /**
     * Returns the ``$entry`` provided to ``__construct`` at creation time.
     * @eturn ResultEntry The ``$entry`` provided to ``__construct`` at creation time.
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Returns the name of current attribute.
     * @return string|null The name of current attribute or ``null`` if the
     *         iterator is invalid (past the end).
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Returns an array of values of the current attribute.
     *
     * Should only be used on valid iterator.
     *
     * @return array an array of values of the current attribute.
     * @link http://php.net/manual/en/iterator.current.php Iterator::current
     */
    public function current()
    {
        return $this->entry->get_values($this->attribute);
    }

    /**
     * Returns the key of the current element (name of current attribute).
     * @return string|null The name of current attribute or ``null`` if the
     *         iterator is invalid (past the end).
     *
     * @link http://php.net/manual/en/iterator.key.php Iterator::key
     */
    public function key()
    {
        return $this->attribute;
    }

    /**
     * Moves the current position to the next element
     *
     * @link http://php.net/manual/en/iterator.next.php Iterator::next
     */
    public function next()
    {
        $next = $this->entry->next_attribute();
        $this->attribute = is_string($next) ? strtolower($next) : $next;
    }

    /**
     * Rewinds back to the first element of the iterator
     *
     * @link http://php.net/manual/en/iterator.rewind.php Iterator::rewind
     */
    public function rewind()
    {
        $first = $this->entry->first_attribute();
        $this->attribute = is_string($first) ? strtolower($first) : $first;
    }

    /**
     * Checks if current position is valid
     *
     * @link http://php.net/manual/en/iterator.valid.php Iterator::valid
     */
    public function valid()
    {
        return is_string($this->attribute);
    }
}

// vim: syntax=php sw=4 ts=4 et:
