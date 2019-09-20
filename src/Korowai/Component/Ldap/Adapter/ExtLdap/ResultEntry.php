<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/ResultEntry.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\AbstractResultEntry;
use Korowai\Component\Ldap\Adapter\ResultAttributeIteratorInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultAttributeIterator;

/**
 * Wrapper for ldap entry result resource.
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultEntry extends AbstractResultEntry
{
    /** @var ResultEntry */
    private $entry;
    private $result;
    private $iterator;

    /**
     * Initializes the ``ResultEntry`` instance
     *
     * @param resource|null $entry
     * @param Result $result
     */
    public function __construct($entry, Result $result)
    {
        $this->entry = $entry;
        $this->result= $result;
    }

    /**
     * Return the underlying resource identifier.
     *
     * @return resource
     */
    public function getResource()
    {
        return $this->entry;
    }

    /**
     * Return the Result object which contains the entry.
     *
     * @return resource
     */
    public function getResult()
    {
        return $this->result;
    }

    // @codingStandardsIgnoreStart
    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName

    /**
     * Return first attribute
     *
     * @link http://php.net/manual/en/function.ldap-first-attribute.php ldap_first_attribute()
     */
    public function first_attribute()
    {
        return $this->result->getLink()->first_attribute($this);
    }

    /**
     * Get attributes from a search result entry
     *
     * @link http://php.net/manual/en/function.ldap-get-attributes.php ldap_get_attributes()
     */
    public function get_attributes()
    {
        return $this->result->getLink()->get_attributes($this);
    }

    /**
     * Get the DN of a result entry
     *
     * @link http://php.net/manual/en/function.ldap-get-dn.php ldap_get_dn()
     */
    public function get_dn()
    {
        return $this->result->getLink()->get_dn($this);
    }

    /**
     * Get all binary values from a result entry
     *
     * @link http://php.net/manual/en/function.ldap-get-values-len.php ldap_get_values_len()
     */
    public function get_values_len(string $attribute)
    {
        return $this->result->getLink()->get_values_len($this, $attribute);
    }

    /**
     * Get all values from a result entry
     *
     * @link http://php.net/manual/en/function.ldap-get-values.php ldap_get_values()
     */
    public function get_values($attribute)
    {
        return $this->result->getLink()->get_values($this, $attribute);
    }

    /**
     * Get the next attribute in result
     *
     * @link http://php.net/manual/en/function.ldap-next-attribute.php ldap_next_attribute()
     */
    public function next_attribute()
    {
        return $this->result->getLink()->next_attribute($this);
    }

    /**
     * Get next result entry
     *
     * @link http://php.net/manual/en/function.ldap-next-entry.php ldap_next_entry()
     */
    public function next_entry()
    {
        return $this->result->getLink()->next_entry($this);
    }

    // phpcs:enable Generic.NamingConventions.CamelCapsFunctionName
    // @codingStandardsIgnoreEnd

    /**
     * It always returns same instance. When used for the first
     * time, the iterator is set to point to the first attribute of the entry.
     * For subsequent calls, the method just return the iterator without
     * altering its position.
     */
    public function getAttributeIterator() : ResultAttributeIteratorInterface
    {
        if (!isset($this->iterator)) {
            $first = $this->first_attribute();
            $this->iterator = new ResultAttributeIterator($this, $first);
        }
        return $this->iterator;
    }


    /**
     * {@inheritdoc}
     */
    public function getDn() : string
    {
        return $this->get_dn();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes() : array
    {
        $attribs = array_filter($this->get_attributes(), function ($key) {
            return is_string($key) && ($key != "count");
        }, ARRAY_FILTER_USE_KEY);
        array_walk($attribs, function (&$value) {
            unset($value['count']);
        });
        return array_change_key_case($attribs, CASE_LOWER);
    }
}

// vim: syntax=php sw=4 ts=4 et:
