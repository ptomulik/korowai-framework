<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultTest.php
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
use \Phake;

use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntryIterator;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReferenceIterator;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    public function getLdapFunctionMock(...$args)
    {
        return $this->getFunctionMock('\\Korowai\\Component\\Ldap\\Adapter\ExtLdap', ...$args);
    }

    public function test_getResource()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);
        $this->assertSame('ldap result', $result->getResource());
    }

    public function test_getLink()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);
        $this->assertSame($link, $result->getLink());
    }

    public function test_control_paged_result_response()
    {
        $link = Phake::mock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $callback = function ($result, &...$tail) {
            if(count($tail) > 0) { $tail[0] = 'cookie'; }
            if(count($tail) > 1) { $tail[1] = 123; }
            return 'ok';
        };

        Phake::when($link)->control_paged_result_response(
            $this->isInstanceOf(Result::class),
            Phake::ignoreRemaining()
        )->thenReturnCallback($callback);

        $this->assertSame('ok', $result->control_paged_result_response($cookie, $estimated));

        Phake::verify($link, Phake::times(1))->control_paged_result_response(
            $this->identicalTo($result),
            Phake::ignoreRemaining()
        );

        $this->assertSame('cookie', $cookie);
        $this->assertSame(123, $estimated);
    }

    public function test_count_entries()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('count_entries')
             ->with($this->identicalTo($result))
             ->willReturn(123);
        $this->assertSame(123, $result->count_entries());
    }

    public function test_first_entry()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_entry')
             ->with($this->identicalTo($result))
             ->willReturn('first entry');
        $this->assertSame('first entry', $result->first_entry());
    }

    public function test_first_reference()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_reference')
             ->with($this->identicalTo($result))
             ->willReturn('first reference');
        $this->assertSame('first reference', $result->first_reference());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_free_result()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);
        $this->getLdapFunctionMock('ldap_free_result')
             ->expects($this->once())
             ->with('ldap result')
             ->willReturn('ok');

        $this->assertSame('ok', $result->free_result());
    }

    public function test_get_entries()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('get_entries')
             ->with($this->identicalTo($result))
             ->willReturn(array('entries'));
        $this->assertSame(array('entries'), $result->get_entries());
    }

    public function test_parse_result()
    {
        $link = Phake::mock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $callback = function ($result, &$errcode, &...$tail) {
            $errcode = 2;
            if(count($tail) > 0) { $tail[0] = 'dc=korowai,dc=org'; }
            if(count($tail) > 1) { $tail[1] = 'Error message'; }
            if(count($tail) > 2) { $tail[2] = array('Referrals'); }
            return false;
        };

        Phake::when($link)->parse_result(
            $this->isInstanceOf(Result::class),
            Phake::ignoreRemaining()
        )->thenReturnCallback($callback);

        $this->assertSame(false, $result->parse_result($errcode, $matcheddn, $errmsg, $referrals));

        Phake::verify($link, Phake::times(1))->parse_result(
            $this->identicalTo($result),
            Phake::ignoreRemaining()
        );

        $this->assertSame(2, $errcode);
        $this->assertSame('dc=korowai,dc=org', $matcheddn);
        $this->assertSame('Error message', $errmsg);
        $this->assertSame(array('Referrals'), $referrals);
    }

    public function test_sort()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('sort')
             ->with($this->identicalTo($result), 'sortfilter')
             ->willReturn(array('sorted'));
        $this->assertSame(array('sorted'), $result->sort('sortfilter'));
    }

    public function test_getResultEntryIterator()
    {
        $link = $this->createMock(LdapLink::class);
        $entry = $this->createMock(ResultEntry::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_entry')
             ->with($result)
             ->willReturn($entry);

        $iter = $result->getResultEntryIterator();
        $this->assertSame($result, $iter->getResult());
        $this->assertSame($entry, $iter->getEntry());
    }

    public function test_getResultEntryIterator_NullFirstEntry()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_entry')
             ->with($result)
             ->willReturn(null);

        $iter = $result->getResultEntryIterator();
        $this->assertSame($result, $iter->getResult());
        $this->assertNull($iter->getEntry());
    }

    public function test_getResultReferenceIterator()
    {
        $link = $this->createMock(LdapLink::class);
        $ref = $this->createMock(ResultReference::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_reference')
             ->with($result)
             ->willReturn($ref);

        $iter = $result->getResultReferenceIterator();
        $this->assertSame($result, $iter->getResult());
        $this->assertSame($ref, $iter->getReference());
    }

    public function test_getResultReferenceIterator_NullFirstReference()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);

        $link->expects($this->once())
             ->method('first_reference')
             ->with($result)
             ->willReturn(null);

        $iter = $result->getResultReferenceIterator();
        $this->assertSame($result, $iter->getResult());
        $this->assertNull($iter->getReference());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_destruct_Invalid()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('ldap result', $link);
        $this->getLdapFunctionMock('ldap_free_result')
             ->expects($this->never())
             ->with('ldap result')
             ->willReturn('ok');

        unset($result);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_destruct_Valid()
    {
        $link = $this->createMock(LdapLink::class);
        $result = new Result('FAKE LDAP RESULT', $link);

        $this->getLdapFunctionMock('is_resource')
             ->expects($this->any())
             ->with('FAKE LDAP RESULT')
             ->willReturn(true);

        $this->getLdapFunctionMock('get_resource_type')
             ->expects($this->any())
             ->with('FAKE LDAP RESULT')
             ->willReturn('ldap result');

        $this->getLdapFunctionMock('ldap_free_result')
             ->expects($this->once())
             ->with('FAKE LDAP RESULT')
             ->willReturn(true);

        unset($result);
    }
}

// vim: syntax=php sw=4 ts=4 et:
