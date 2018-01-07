<?php
/**
 * This file is part of the Korowai package
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);
namespace Korowai\Component\Ldap\Tests\Adapter\ExtLdap;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Adapter\ExtLdap\EntryManager;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Entry;
use Korowai\Component\Ldap\Exception\LdapException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class EntryManagerTest extends TestCase
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
        $mngr = new EntryManager($link);
        $this->assertTrue(true); // didn't blow up
    }

    public function test_getLink()
    {
        $link = $this->createMock(LdapLink::class);
        $mngr = new EntryManager($link);
        $this->assertSame($link, $mngr->getLink());
    }

    public function test_add()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('add')
             ->with('dc=korowai,dc=org', $attributes)
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->add($entry));
    }

    /**
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage Uninitialized LDAP link
     */
    public function test_add_UninitializedLink()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(false);
        $link->expects($this->never())
             ->method('add');

        $mngr = new EntryManager($link);
        $mngr->add($entry);
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Error message
     */
    public function test_add_Failure()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('add')
             ->with('dc=korowai,dc=org', $attributes)
             ->willReturn(false);
        $link->method('errno')
             ->willReturn(2);

        $this->getLdapFunctionMock('ldap_err2str')
             ->expects($this->once())
             ->with(2)
             ->willReturn("Error message");

        $mngr = new EntryManager($link);
        $mngr->add($entry);
    }

    public function test_update()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('modify')
             ->with('dc=korowai,dc=org', $attributes)
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->update($entry));
    }

    /**
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage Uninitialized LDAP link
     */
    public function test_update_Invalid()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(false);
        $link->expects($this->never())
             ->method('modify');

        $mngr = new EntryManager($link);
        $mngr->update($entry);
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Error message
     */
    public function test_update_Failure()
    {
        $attributes = array('attr1' => array('attr1val1'));
        $entry = new Entry('dc=korowai,dc=org', $attributes);

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('modify')
             ->with('dc=korowai,dc=org', $attributes)
             ->willReturn(false);
        $link->method('errno')
             ->willReturn(2);

        $this->getLdapFunctionMock('ldap_err2str')
             ->expects($this->once())
             ->with(2)
             ->willReturn("Error message");

        $mngr = new EntryManager($link);
        $mngr->update($entry);
    }

    public function test_rename_Default()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('rename')
             ->with('dc=korowai,dc=org', 'cn=korowai', null, true)
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->rename($entry,'cn=korowai'));
    }

    public function test_rename_DeleteOldRdn()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('rename')
             ->with('dc=korowai,dc=org', 'cn=korowai', null, true)
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->rename($entry,'cn=korowai', true));
    }

    public function test_rename_LeaveOldRdn()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('rename')
             ->with('dc=korowai,dc=org', 'cn=korowai', null, false)
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->rename($entry,'cn=korowai', false));
    }

    /**
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage Uninitialized LDAP link
     */
    public function test_rename_Invalid()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(false);
        $link->expects($this->never())
             ->method('rename');

        $mngr = new EntryManager($link);
        $mngr->rename($entry,'cn=korowai', true);
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Error message
     */
    public function test_rename_Failure()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('rename')
             ->with('dc=korowai,dc=org', 'cn=korowai', null, true)
             ->willReturn(false);
        $link->method('errno')
             ->willReturn(2);

        $this->getLdapFunctionMock('ldap_err2str')
             ->expects($this->once())
             ->with(2)
             ->willReturn("Error message");

        $mngr = new EntryManager($link);
        $mngr->rename($entry,'cn=korowai', true);
    }

    public function test_delete()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('delete')
             ->with('dc=korowai,dc=org')
             ->willReturn(true);

        $mngr = new EntryManager($link);
        $this->assertNull($mngr->delete($entry));
    }

    /**
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode -1
     * @expectedExceptionMessage Uninitialized LDAP link
     */
    public function test_delete_Invalid()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(false);
        $link->expects($this->never())
             ->method('delete');

        $mngr = new EntryManager($link);
        $mngr->delete($entry);
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionCode 2
     * @expectedExceptionMessage Error message
     */
    public function test_delete_Failure()
    {
        $entry = new Entry('dc=korowai,dc=org');

        $link = $this->createLdapLinkMock(true);
        $link->expects($this->once())
             ->method('delete')
             ->with('dc=korowai,dc=org')
             ->willReturn(false);
        $link->method('errno')
             ->willReturn(2);

        $this->getLdapFunctionMock('ldap_err2str')
             ->expects($this->once())
             ->with(2)
             ->willReturn("Error message");

        $mngr = new EntryManager($link);
        $mngr->delete($entry);
    }
}

// vim: syntax=php sw=4 ts=4 et:
