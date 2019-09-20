<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultReferenceTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use \Phake;

use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;


/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultReferenceTest extends TestCase
{
    private function getResultMock($link = null)
    {
        $result = $this->createMock(Result::class);
        $result->expects($this->any())
               ->method('getLink')
               ->with()
               ->willReturn($link);
        return $result;
    }

    public function test_getResource()
    {
        $result = $this->getResultMock();
        $ref = new ResultReference('ldap reference', $result);
        $this->assertSame('ldap reference', $ref->getResource());
    }

    public function test_getResult()
    {
        $result = $this->getResultMock();
        $ref = new ResultReference('ldap reference', $result);
        $this->assertSame($result, $ref->getResult());
    }

    public function test_next_reference()
    {
        $link = $this->createMock(LdapLink::class);
        $result = $this->getResultMock($link);

        $ref = new ResultReference('ldap reference', $result);

        $link->expects($this->once())
             ->method('next_reference')
             ->with($this->identicalTo($ref))
             ->willReturn('next reference');

        $this->assertSame('next reference', $ref->next_reference());
    }

    public function test_parse_reference()
    {
        $link = Phake::mock(LdapLink::class);
        $result = $this->getResultMock($link);

        $ref = new ResultReference('ldap reference', $result);

        $callback = function ($ref, &$referrals) {
            $referrals = array('Referrals');
            return 'ok';
        };

        Phake::when($link)->parse_reference(
            $this->isInstanceOf(ResultReference::class),
            Phake::ignoreRemaining()
        )->thenReturnCallback($callback);

        $this->assertSame('ok', $ref->parse_reference($referrals));

        Phake::verify($link, Phake::times(1))->parse_reference(
            $this->identicalTo($ref),
            Phake::ignoreRemaining()
        );

        $this->assertSame(array('Referrals'), $referrals);
    }

    public function test_getReferrals_Failure()
    {
        $link = Phake::mock(LdapLink::class);
        $result = $this->getResultMock($link);

        $ref = new ResultReference('ldap reference', $result);

        Phake::when($link)->parse_reference(
            $this->isInstanceOf(ResultReference::class),
            Phake::ignoreRemaining()
        )->thenReturn(false);

        $this->assertFalse($ref->getReferrals());
    }

    public function test_getReferrals_Success()
    {
        $link = Phake::mock(LdapLink::class);
        $result = $this->getResultMock($link);

        $ref = new ResultReference('ldap reference', $result);

        $callback = function ($ref, &$referrals) {
            $referrals = array('Referrals');
            return true;
        };

        Phake::when($link)->parse_reference(
            $this->isInstanceOf(ResultReference::class),
            Phake::ignoreRemaining()
        )->thenReturnCallback($callback);

        $this->assertSame(array('Referrals'), $ref->getReferrals());
    }
}

// vim: syntax=php sw=4 ts=4 et:
