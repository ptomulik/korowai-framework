<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/AbstractResultEntryTest.php
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
use Korowai\Component\Ldap\Adapter\AbstractResultEntry;
use Korowai\Component\Ldap\Entry;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractResultEntryTest extends TestCase
{
    private function getAbstractResultEntryMock($ctor = true, array $methods = array())
    {
        $builder = $this->getMockBuilder(AbstractResultEntry::class);

        if(!$ctor) {
            $builder->disableOriginalConstructor();
        } elseif(is_array($ctor)) {
            $builder->setConstructorArgs($ctor);
        }

        foreach(['getDn', 'getAttributes'] as $method) {
            if(!in_array($method, $methods)) {
                $methods[] = $method;
            }
        }
        $builder->setMethods($methods);
        return $builder->getMockForAbstractClass();
    }

    public function test_toEntry()
    {
        $dn = 'uid=jsmith,ou=people,dc=korowai,dc=org';
        $attributes = array(
            'uid' => array('jsmith'),
            'firstName' => array('John'),
            'sn' => array('Smith')
        );

        $abstract= $this->getAbstractResultEntryMock();
        $abstract->expects($this->once())
                 ->method('getDn')
                 ->with()
                 ->willReturn($dn);
        $abstract->expects($this->once())
                 ->method('getAttributes')
                 ->with()
                 ->willReturn($attributes);

        $entry = $abstract->toEntry();

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertEquals($dn, $entry->getDn());
        $this->assertSame($attributes, $entry->getAttributes());
    }
}

// vim: syntax=php sw=4 ts=4 et:
