<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Adapter\ExtLdap;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Adapter\ExtLdap\EnsureLdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Exception\LdapException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class EnsureLdapLinkTest extends TestCase
{
    use EnsureLdapLink;

    /**
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionMessage Uninitialized LDAP link
     * @expectedExceptionCode -1
     */
    public function test_ensureLdapLink_Failure()
    {
      $link = $this->createMock(LdapLink::class);
      $link->expects($this->once())
           ->method('isValid')
           ->with()
           ->willReturn(false);
      static::ensureLdapLink($link);
    }

    public function test_ensureLdapLink_Success()
    {
      $link = $this->createMock(LdapLink::class);
      $link->expects($this->once())
           ->method('isValid')
           ->with()
           ->willReturn(true);
      $this->assertTrue(static::ensureLdapLink($link));
    }
}

// vim: syntax=php sw=4 ts=4 et:
