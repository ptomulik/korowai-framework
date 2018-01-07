<?php
/**
 * This file is part of the Korowai package
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);
namespace Korowai\Component\Ldap\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Exception\LdapException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class LdapExceptionTest extends TestCase
{
    public function testBaseClass()
    {
        $this->assertInstanceOf(\RuntimeException::class, new LdapException());
    }

    public function test_getMessage()
    {
        $e = new LdapException("Custom message");
        $this->assertEquals("Custom message", $e->getMessage());
    }
}

// vim: syntax=php sw=4 ts=4 et:
