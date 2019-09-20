<?php
/**
 * @file src/Korowai/Component/Ldap/Entry.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap;

use Korowai\Component\Ldap\Exception\AttributeException;

/**
 * Represents single ldap entry with DN and attributes
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class Entry
{
    private $dn;
    private $attributes;

    /**
     * Entry's constructor.
     *
     * @throws \TypeError
     */
    public function __construct(string $dn, array $attributes = array())
    {
        $this->setDn($dn);
        $this->validateAttributes($attributes);
        $this->attributes = $attributes;
    }

    /**
     * Retuns the entry's DN.
     *
     * @return string
     */
    public function getDn() : string
    {
        return $this->dn;
    }

    /**
     * Sets the entry's DN.
     *
     * @param string $dn
     * @throws \TypeError
     */
    public function setDn(string $dn)
    {
        $this->validateDn($dn);
        $this->dn = $dn;
    }

    /**
     * Validates string provided as DN.
     *
     * @param string $dn
     * @throws \TypeError
     */
    public function validateDn(string $dn)
    {
    }

    /**
     * Returns the complete array of attributes
     *
     * @return array
     */
    public function getAttributes() : array
    {
        return $this->attributes;
    }

    /**
     * Returns a specific attribute's values
     *
     * @param string $name
     *
     * @throws AttributeException
     * @return array
     */
    public function getAttribute(string $name) : array
    {
        $this->ensureAttributeExists($name);
        return $this->attributes[$name];
    }

    /**
     * Throws AttributeException if given attribute does not exist
     *
     * @throws AttributeException
     */
    public function ensureAttributeExists(string $name)
    {
        if (!$this->hasAttribute($name)) {
            $msg = "Entry '" . $this->dn . "' has no attribute '". $name ."'";
            throw new AttributeException($msg);
        }
    }

    /**
     * Retuns whether an attribute exists.
     *
     * @return bool
     */
    public function hasAttribute(string $name) : bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Sets attributes.
     *
     * For each attribute in $attributes, if attribute already exists in Entry,
     * its values will be replaced with values provided in $attributes. If
     * there is no attribute in Entry, it'll be added to Entry.
     *
     * @param array $attributes
     * @throws AttributeException
     */
    public function setAttributes(array $attributes)
    {
        $this->validateAttributes($attributes);
        foreach ($attributes as $name => $values) {
            $this->attributes[$name] = $values;
        }
    }

    /**
     * Check if the given array of attributes can be safely assigned to entry.
     *
     * If not, an exception is thrown.
     *
     * @throws \TypeError
     */
    public function validateAttributes(array $attributes)
    {
        foreach ($attributes as $name => $values) {
            $this->validateAttribute($name, $values);
        }
    }

    /**
     * Sets values for the given attribute
     *
     * @param string $name
     * @param array $values
     */
    public function setAttribute(string $name, array $values)
    {
        $this->validateAttribute($name, $values);
        $this->attributes[$name] = $values;
    }

    /**
     * Currently only check the types of attribute name and values
     *
     * @throws \TypeError
     */
    public function validateAttribute(string $name, array $values)
    {
    }
}

// vim: syntax=php sw=4 ts=4 et:
