<?php
/**
 * This file is part of the Korowai package
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);
namespace Korowai\Component\Ldap\Exception;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AttributeException extends \OutOfRangeException
{
    protected $message = "No such attribute";
}

// vim: syntax=php sw=4 ts=4 et:
