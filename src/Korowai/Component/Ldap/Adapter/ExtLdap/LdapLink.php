<?php
/**
 * @file src/Korowai/Component/Ldap/Adapter/ExtLdap/LdapLink.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Adapter\ExtLdap;

use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;

/**
 * Wrapper class for "ldap link" resource.
 *
 * The "ldap link" resource handle is returned by ldap_connect().
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class LdapLink
{
    private $link;

    /**
     * Return whether $arg is a valid "ldap link" resource.
     *
     * @param mixed $arg An argument to be examined.
     * @return bool
     *
     * @link http://php.net/manual/en/resource.php
     */
    public static function isLdapLinkResource($arg) : bool
    {
        // The name "ldap link" is documented: http://php.net/manual/en/resource.php
        return is_resource($arg) && (get_resource_type($arg) === "ldap link");
    }

    /**
     * Constructs LdapLink
     *
     * @param resource $link Should be a resource returned by ldap_connect().
     * @link http://php.net/manual/en/function.ldap-connect.php ldap_connect()
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    /**
     * Destructs LdapLink
     */
    public function __destruct()
    {
        if ($this->isValid()) {
            $this->unbind();
        }
    }

    /**
     * Returns resource provided to __construct().
     * @return resource|null
     */
    public function getResource()
    {
        return $this->link;
    }

    /**
     * Return whether $this->link is a valid "ldap link" resource.
     *
     * @return bool
     */
    public function isValid() : bool
    {
        return static::isLdapLinkResource($this->link);
    }

    // @codingStandardsIgnoreStart
    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName

    /**
     * Add entries to LDAP directory
     *
     * @param string $dn
     * @param array  $entry
     *
     * @link http://php.net/manual/en/function.ldap-add.php ldap_add()
     */
    public function add($dn, $entry)
    {
        return @ldap_add($this->link, $dn, $entry);
    }

    /**
     * Bind to LDAP directory
     *
     * @param string $bind_rdn
     * @param string $bind_password
     *
     * @link http://php.net/manual/en/function.ldap-bind.php ldap_bind()
     */
    public function bind($bind_rdn = null, $bind_password = null)
    {
        $args = func_get_args();
        return @ldap_bind($this->link, ...$args);
    }

    /**
     * Same as ldap_close
     *
     * @link http://php.net/manual/en/function.ldap-close.php ldap_close()
     */
    public function close()
    {
        return @ldap_close($this->link);
    }

    /**
     * Compare value of attribute found in entry specified with DN
     *
     * @param string $dn
     * @param string $attribute
     * @param string $value
     *
     * @link http://php.net/manual/en/function.ldap-compare.php ldap_compare()
     */
    public function compare($dn, $attribute, $value)
    {
        return @ldap_compare($this->link, $dn, $attribute, $value);
    }

    /**
     * Connect to an LDAP server
     *
     * @link http://php.net/manual/en/function.ldap-connect.php ldap_connect()
     */
    public static function connect(...$args)
    {
        $res = @ldap_connect(...$args);
        return $res ? new LdapLink($res) : $res;
    }

    /**
     * Retrieve the LDAP pagination cookie
     *
     * @param Result $result
     * @param $tail
     *
     * @link http://php.net/manual/en/function.ldap-control-paged-result-response.php ldap_control_paged_result_response()
     */
    public function control_paged_result_response(Result $result, &...$tail)
    {
        return @ldap_control_paged_result_response($this->link, $result->getResource(), ...$tail);
    }

    /**
     * Send LDAP pagination control
     *
     * @param int $pagesize
     * @param mixed $tail remaining arguments passed to ldap_control_paged_result().
     *
     * @link http://php.net/manual/en/function.ldap-control-paged-result.php ldap_control_paged_result()
     */
    public function control_paged_result($pagesize, ...$tail)
    {
        return @ldap_control_paged_result($this->link, $pagesize, ...$tail);
    }

    /**
     * Count the number of entries in a search
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-count-entries.php ldap_count_entries()
     */
    public function count_entries(Result $result)
    {
        return @ldap_count_entries($this->link, $result->getResource());
    }

    /**
     * Count the number of references in a search
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-count-references.php ldap_count_references()
     */
    public function count_references(Result $result)
    {
        throw new \BadMethodCallException("Not implemented");
        //return @ldap_count_references($this->link, $result->getResource());
    }

    /**
     * Delete an entry from a directory
     *
     * @param string $dn
     *
     * @link http://php.net/manual/en/function.ldap-delete.php ldap_delete()
     */
    public function delete($dn)
    {
        return @ldap_delete($this->link, $dn);
    }

    /**
     * Convert DN to User Friendly Naming format
     *
     * @param string $dn
     *
     * @link http://php.net/manual/en/function.ldap-dn2ufn.php ldap_dn2ufn()
     */
    public static function dn2ufn($dn)
    {
        return @ldap_dn2ufn($dn);
    }

    /**
     * Convert LDAP error number into string error message
     *
     * @param int $errno
     *
     * @link http://php.net/manual/en/function.ldap-err2str.php ldap_err2str()
     */
    public static function err2str($errno)
    {
        return @ldap_err2str($errno);
    }

    /**
     * Return the LDAP error number of the last LDAP command
     *
     * @link http://php.net/manual/en/function.ldap-errno.php ldap_errno()
     */
    public function errno()
    {
        return @ldap_errno($this->link);
    }

    /**
     * Return the LDAP error message of the last LDAP command
     *
     * @link http://php.net/manual/en/function.ldap-error.php ldap_error()
     */
    public function error()
    {
        return @ldap_error($this->link);
    }

    /**
     * Escape a string for use in an LDAP filter or DN
     *
     * @param string $value
     * @param mixed $tail remaining arguments passed to ldap_escape()
     *
     * @link http://php.net/manual/en/function.ldap-escape.php ldap_escape()
     */
    public static function escape($value, ...$tail)
    {
        return @ldap_escape($value, ...$tail);
    }

    /**
     * Splits DN into its component parts
     *
     * @param string $dn
     * @param int with_attrib
     *
     * @link http://php.net/manual/en/function.ldap-explode-dn.php ldap_explode_dn()
     */
    public static function explode_dn($dn, $with_attrib)
    {
        return @ldap_explode_dn($dn, $with_attrib);
    }

    /**
     * Return first attribute
     *
     * @param ResultEntry $result_entry
     *
     * @link http://php.net/manual/en/function.ldap-first-attribute.php ldap_first_attribute()
     */
    public function first_attribute(ResultEntry $result_entry)
    {
        return @ldap_first_attribute($this->link, $result_entry->getResource());
    }

    /**
     * Return first result id
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-first-entry.php ldap_first_entry()
     */
    public function first_entry(Result $result)
    {
        $res = @ldap_first_entry($this->link, $result->getResource());
        return $res ? new ResultEntry($res, $result) : $res;
    }

    /**
     * Return first reference
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-first-reference.php ldap_first_reference()
     */
    public function first_reference(Result $result)
    {
        $res = @ldap_first_reference($this->link, $result->getResource());
        return $res ? new ResultReference($res, $result) : $res;
    }

    /**
     * Free result memory
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-free-result.php ldap_free_result()
     */
    public static function free_result(Result $result)
    {
        return @ldap_free_result($result->getResource());
    }

    /**
     * Get attributes from a search result entry
     *
     * @param ResultEntry $result_entry
     *
     * @link http://php.net/manual/en/function.ldap-get-attributes.php ldap_get_attributes()
     */
    public function get_attributes(ResultEntry $result_entry)
    {
        return @ldap_get_attributes($this->link, $result_entry->getResource());
    }

    /**
     * Get the DN of a result entry
     *
     * @param ResultEntry $result_entry
     *
     * @link http://php.net/manual/en/function.ldap-get-dn.php ldap_get_dn()
     */
    public function get_dn(ResultEntry $result_entry)
    {
        return @ldap_get_dn($this->link, $result_entry->getResource());
    }

    /**
     * Get all result entries
     *
     * @param Result $result
     *
     * @link http://php.net/manual/en/function.ldap-get-entries.php ldap_get_entries()
     */
    public function get_entries(Result $result)
    {
        return @ldap_get_entries($this->link, $result->getResource());
    }

    /**
     * Get the current value for given option
     *
     * @param int $option
     * @param mixed $retval
     *
     * @link http://php.net/manual/en/function.ldap-get-option.php ldap_get_option()
     */
    public function get_option($option, &$retval)
    {
        return @ldap_get_option($this->link, $option, $retval);
    }

    /**
     * Get all binary values from a result entry
     *
     * @link http://php.net/manual/en/function.ldap-get-values-len.php ldap_get_values_len()
     */
    public function get_values_len(ResultEntry $result_entry, string $attribute)
    {
        return @ldap_get_values_len($this->link, $result_entry->getResource(), $attribute);
    }

    /**
     * Get all values from a result entry
     *
     * @param ResultEntry $result_entry
     * @param string $attribute
     *
     * @link http://php.net/manual/en/function.ldap-get-values.php ldap_get_values()
     */
    public function get_values(ResultEntry $result_entry, $attribute)
    {
        return @ldap_get_values($this->link, $result_entry->getResource(), $attribute);
    }

    /**
     * Single-level search
     *
     * @param string $base_dn
     * @param string $filter
     * @param mixed $tail remaining arguments passed to ldap_list()
     *
     * @link http://php.net/manual/en/function.ldap-list.php ldap_list()
     */
    public function list($base_dn, $filter, ...$tail)
    {
        $res = @ldap_list($this->link, $base_dn, $filter, ...$tail);
        return $res ? new Result($res, $this) : $res;
    }

    /**
     * Add attribute values to current attributes
     *
     * @param string $dn
     * @param array $entry
     *
     * @link http://php.net/manual/en/function.ldap-mod-add.php ldap_mod_add()
     */
    public function mod_add($dn, $entry)
    {
        return @ldap_mod_add($this->link, $dn, $entry);
    }

    /**
     * Delete attribute values from current attributes
     *
     * @param string $dn
     * @param array $entry
     *
     * @link http://php.net/manual/en/function.ldap-mod-del.php ldap_mod_del()
     */
    public function mod_del($dn, $entry)
    {
        return @ldap_mod_del($this->link, $dn, $entry);
    }

    /**
     * Replace attribute values with new ones
     *
     * @param string $dn
     * @param array $entry
     *
     * @link http://php.net/manual/en/function.ldap-mod-replace.php ldap_mod_replace()
     */
    public function mod_replace($dn, $entry)
    {
        return @ldap_mod_replace($this->link, $dn, $entry);
    }

    /**
     * Batch and execute modifications on an LDAP entry
     *
     * @param string $dn
     * @param array $entry
     *
     * @link http://php.net/manual/en/function.ldap-modify-batch.php ldap_modify_batch()
     */
    public function modify_batch($dn, $entry)
    {
        return @ldap_modify_batch($this->link, $dn, $entry);
    }

    /**
     * Modify an LDAP entry
     *
     * @param string $dn
     * @param array $entry
     *
     * @link http://php.net/manual/en/function.ldap-modify.php ldap_modify()
     */
    public function modify($dn, $entry)
    {
        return @ldap_modify($this->link, $dn, $entry);
    }

    /**
     * Get the next attribute in result
     *
     * @param ResultEntry $result_entry
     *
     * @link http://php.net/manual/en/function.ldap-next-attribute.php ldap_next_attribute()
     */
    public function next_attribute(ResultEntry $result_entry)
    {
        return @ldap_next_attribute($this->link, $result_entry->getResource());
    }

    /**
     * Get next result entry
     *
     * @param ResultEntry $result_entry
     *
     * @link http://php.net/manual/en/function.ldap-next-entry.php ldap_next_entry()
     */
    public function next_entry(ResultEntry $result_entry)
    {
        $res = @ldap_next_entry($this->link, $result_entry->getResource());
        return $res ? new ResultEntry($res, $result_entry->getResult()) : $res;
    }

    /**
     * Get next reference
     *
     * @param ResultReference $reference
     *
     * @link http://php.net/manual/en/function.ldap-next-reference.php ldap_next_reference()
     */
    public function next_reference(ResultReference $reference)
    {
        $res = @ldap_next_reference($this->link, $reference->getResource());
        return $res ? new ResultReference($res, $reference->getResult()) : $res;
    }

    /**
     * Extract information from reference entry
     *
     * @param ResultReference $reference
     * @param array &$referrals
     *
     * @link http://php.net/manual/en/function.ldap-parse-reference.php ldap_parse_reference()
     */
    public function parse_reference(ResultReference $reference, &$referrals)
    {
        return @ldap_parse_reference($this->link, $reference->getResource(), $referrals);
    }

    /**
     * Extract information from result
     *
     * @param Result $result
     * @param int &$errcode
     * @param mixed $tail remaining arguments passed to ldap_parse_result()
     *
     * @link http://php.net/manual/en/function.ldap-parse-result.php ldap_parse_result()
     */
    public function parse_result(Result $result, &$errcode, &...$tail)
    {
        return @ldap_parse_result($this->link, $result->getResource(), $errcode, ...$tail);
    }

    /**
     * Read an entry
     *
     * @param string $base_dn
     * @param string $filter
     * @param mixed $tail remaining arguments passed to ldap_read()
     *
     * @link http://php.net/manual/en/function.ldap-read.php ldap_read()
     */
    public function read($base_dn, $filter, ...$tail)
    {
        $res = @ldap_read($this->link, $base_dn, $filter, ...$tail);
        return $res ? new Result($res, $this) : $res;
    }

    /**
     * Modify the name of an entry
     *
     * @param string $dn
     * @param string $newrdn
     * @param string $newparent
     * @param bool $deleteoldrdn
     *
     * @link http://php.net/manual/en/function.ldap-rename.php ldap_rename()
     */
    public function rename($dn, $newrdn, $newparent, $deleteoldrdn)
    {
        return @ldap_rename($this->link, $dn, $newrdn, $newparent, $deleteoldrdn);
    }

    /**
     * Bind to LDAP directory using SASL
     *
     * @link http://php.net/manual/en/function.ldap-sasl-bind.php ldap_sasl_bind()
     */
    public function sasl_bind(...$args)
    {
        return @ldap_sasl_bind($this->link, ...$args);
    }

    /**
     * Search LDAP tree
     *
     * @param string $base_dn
     * @param string $filter
     * @param mixed $tail remaining arguments passed to ldap_search()
     *
     * @link http://php.net/manual/en/function.ldap-search.php ldap_search()
     */
    public function search($base_dn, $filter, ...$tail)
    {
        $res = @ldap_search($this->link, $base_dn, $filter, ...$tail);
        return $res ? new Result($res, $this) : $res;
    }

    /**
     * Set the value of the given option
     *
     * @param int $option
     * @param mixed $newval
     *
     * @link http://php.net/manual/en/function.ldap-set-option.php ldap_set_option()
     */
    public function set_option($option, $newval)
    {
        return @ldap_set_option($this->link, $option, $newval);
    }

    /**
     * Set a callback function to do re-binds on referral chasing
     *
     * @param callable $callback
     *
     * @link http://php.net/manual/en/function.ldap-set-rebind-proc.php ldap_set_rebind_proc()
     */
    public function set_rebind_proc($callback)
    {
        return @ldap_set_rebind_proc($this->link, $callback);
    }

    /**
     * Sort LDAP result entries on the client side
     *
     * @param Result $result
     * @param string $sortfilter
     *
     * @link http://php.net/manual/en/function.ldap-sort.php ldap_sort()
     */
    public function sort(Result $result, $sortfilter)
    {
        return @ldap_sort($this->link, $result->getResource(), $sortfilter);
    }

    /**
     * Start TLS
     *
     * @link http://php.net/manual/en/function.ldap-start-tls.php ldap_start_tls()
     */
    public function start_tls()
    {
        return @ldap_start_tls($this->link);
    }

    /**
     * Unbind from LDAP directory
     *
     * @link http://php.net/manual/en/function.ldap-unbind.php ldap_unbind()
     */
    public function unbind()
    {
        return @ldap_unbind($this->link);
    }

    // phpcs:enable Generic.NamingConventions.CamelCapsFunctionName
    // @codingStandardsIgnoreEnd
}

// vim: syntax=php sw=4 ts=4 et:
