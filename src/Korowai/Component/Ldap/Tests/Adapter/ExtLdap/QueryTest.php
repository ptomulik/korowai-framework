<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/QueryTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Adapter\ExtLdap;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Adapter\ExtLdap\Query;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ResultInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class QueryTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    public function getLdapFunctionMock(...$args)
    {
        return $this->getFunctionMock('\\Korowai\\Component\\Ldap\\Adapter\ExtLdap', ...$args);
    }

    public function createLdapLinkMock($valid, $unbind = true)
    {
        $link = $this->createMock(LdapLink::class);
        if($valid === true || $valid === false) {
            $link->method('isValid')->willReturn($valid);
        }
        if($unbind === true || $unbind === false) {
            $link->method('unbind')->willReturn($unbind);
        }
        return $link;
    }

    public function test_construct()
    {
        $link = $this->createMock(LdapLink::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*");
        $this->assertTrue(true); // didn't blow up
    }

    public function test_getLink()
    {
        $link = $this->createMock(LdapLink::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*");
        $this->assertSame($link, $query->getLink());
    }

    public function test_execute_base()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'base'));
        $link->expects($this->exactly(2))
             ->method('read')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->never())
             ->method('search');
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->getResult());
    }

    public function test_execute_one()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'one'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->exactly(2))
             ->method('list')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $link->expects($this->never())
             ->method('search');
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->getResult());
    }

    public function test_execute_sub()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'sub'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->exactly(2))
             ->method('search')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->execute());
        $this->assertSame($result, $query->getResult());
    }

    public function test_execute_UninitializedLink()
    {
        $link = $this->createLdapLinkMock(false);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'sub'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->never())
             ->method('search');

        $this->expectException(\Korowai\Component\Ldap\Exception\LdapException::class);
        $this->expectExceptionCode(-1);
        $this->expectExceptionMessage('Uninitialized LDAP link');

        $this->assertSame($result, $query->execute());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_execute_Failure()
    {
        $link = $this->createLdapLinkMock(true);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'sub'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->once())
             ->method('search')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn(false);

        $link->method('errno')
             ->willReturn(2);
        $this->getLdapFunctionMock('ldap_err2str')
             ->expects($this->once())
             ->with(2)
             ->willReturn("Error message");

        $this->expectException(\Korowai\Component\Ldap\Exception\LdapException::class);
        $this->expectExceptionCode(2);
        $this->expectExceptionMessage('Error message');

        $query->execute();
    }

    public function test_getResult_base()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'base'));
        $link->expects($this->once())
             ->method('read')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->never())
             ->method('search');
        $this->assertSame($result, $query->getResult());
        $this->assertSame($result, $query->getResult());
    }

    public function test_getResult_one()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'one'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->once())
             ->method('list')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $link->expects($this->never())
             ->method('search');
        $this->assertSame($result, $query->getResult());
        $this->assertSame($result, $query->getResult());
    }

    public function test_getResult_sub()
    {
        $link = $this->createLdapLinkMock(true);
        $result = $this->createMock(ResultInterface::class);
        $query = new Query($link, "dc=korowai,dc=org", "objectClass=*", array('scope' => 'sub'));
        $link->expects($this->never())
             ->method('read');
        $link->expects($this->never())
             ->method('list');
        $link->expects($this->once())
             ->method('search')
             ->with("dc=korowai,dc=org", "objectClass=*", array("*"), 0, 0, 0, LDAP_DEREF_NEVER)
             ->willReturn($result);
        $this->assertSame($result, $query->getResult());
        $this->assertSame($result, $query->getResult());
    }
}

// vim: syntax=php sw=4 ts=4 et:
