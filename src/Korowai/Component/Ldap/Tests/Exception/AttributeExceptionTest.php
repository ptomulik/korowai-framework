<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Exception/AttributeExceptionTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Exception\AttributeException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AttributeExceptionTest extends TestCase
{
    public function testBaseClass()
    {
        $this->assertInstanceOf(\OutOfRangeException::class, new AttributeException());
    }

    public function test_getMessage_DefaultMessage()
    {
        $e = new AttributeException();
        $this->assertEquals("No such attribute", $e->getMessage());
    }

    public function test_getMessage_CustomMessage()
    {
        $e = new AttributeException("Custom message");
        $this->assertEquals("Custom message", $e->getMessage());
    }
}

// vim: syntax=php sw=4 ts=4 et:
