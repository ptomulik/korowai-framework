<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/AbstractLdapTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\AbstractLdap;
use Korowai\Component\Ldap\LdapInterface;
use Korowai\Component\Ldap\Adapter\QueryInterface;
use Korowai\Component\Ldap\Adapter\ResultInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractLdapTest extends TestCase
{
    public function test__implements__LdapInterface()
    {
        $interfaces = class_implements(AbstractLdap::class);
        $this->assertContains(LdapInterface::class, $interfaces);
    }

    public function test__query()
    {
        $result = $this->getMockBuilder(ResultInterface::class)
                       ->getMockForAbstractClass();

        $query = $this->getMockBuilder(QueryInterface::class)
                      ->setMethods(['getResult'])
                      ->getMockForAbstractClass();

        $query->expects($this->once())
              ->method('getResult')
              ->with()
              ->willReturn($result);

        $ldap = $this->getMockBuilder(AbstractLdap::class)
                     ->setMethods(['createQuery'])
                     ->getMockForAbstractClass();

        $args = [ 'dc=example,dc=org', '(objectClass=*)', ['foo'] ];
        $ldap->expects($this->once())
             ->method('createQuery')
             ->with(...$args)
             ->willReturn($query);

        $this->assertSame($result, $ldap->query(...$args));
    }
}

// vim: syntax=php sw=4 ts=4 et:
