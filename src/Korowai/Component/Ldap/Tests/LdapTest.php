<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Ldap;
use Korowai\Component\Ldap\Adapter\AdapterInterface;
use Korowai\Component\Ldap\Adapter\BindingInterface;
use Korowai\Component\Ldap\Adapter\AdapterFactoryInterface;
use Korowai\Component\Ldap\Adapter\EntryManagerInterface;
use Korowai\Component\Ldap\Entry;

use Korowai\Component\Ldap\Adapter\QueryInterface;
use Korowai\Component\Ldap\Adapter\ExtLdap\Adapter;

class SomeClass {}

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class LdapTest extends TestCase
{
    private function getAdapterFactoryMock()
    {
        return $this->getMockBuilder(AdapterFactoryInterface::class)
                    ->setMethods(['configure', 'createAdapter'])
                    ->getMock();;
    }

    private function getAdapterMock()
    {
        return $this->getMockBuilder(AdapterInterface::class)
                    ->setMethods(['getBinding', 'getEntryManager', 'createQuery'])
                    ->getMock();
    }

    private function getBindingMock()
    {
        return $this->getMockBuilder(BindingInterface::class)
                    ->setMethods(['bind', 'isBound', 'unbind'])
                    ->getMock();
    }

    private function getEntryManagerMock()
    {
        return $this->getMockBuilder(EntryManagerInterface::class)
                    ->setMethods(['add', 'update', 'rename', 'delete'])
                    ->getMock();
    }

    private function getQueryMock()
    {
        return $this->getMockBuilder(QueryInterface::class)
                    ->setMethods(['getResult', 'execute'])
                    ->getMock();
    }

    public function test_createWithAdapterFactory()
    {
        $factory = $this->getAdapterFactoryMock();
        $adapter = $this->getAdapterMock();

        $factory->expects($this->never())
                ->method('configure');

        $factory->expects($this->once())
                ->method('createAdapter')
                ->with()
                ->willReturn($adapter);

        $ldap = Ldap::createWithAdapterFactory($factory);
        $this->assertSame($adapter, $ldap->getAdapter());
    }

    public function test_createWithConfig_Defaults()
    {
        $ldap = Ldap::createWithConfig();
        $this->assertInstanceOf(Adapter::class, $ldap->getAdapter());
    }

    public function test_createWithConfig()
    {
        $ldap = Ldap::createWithConfig(array('host' => 'korowai.org'));
        $this->assertInstanceOf(Adapter::class, $ldap->getAdapter());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid argument 2 to Korowai\Component\Ldap\Ldap::createWithConfig: Foobar is not a name of existing class
     */
    public function test_createWithConfig_InexistentClass()
    {
        $ldap = Ldap::createWithConfig(array(), 'Foobar');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Invalid argument 2 to Korowai\Component\Ldap\Ldap::createWithConfig: Korowai\Component\Ldap\Tests\SomeClass is not an implementation of Korowai\Component\Ldap\Adapter\AdapterFactoryInterface
     */
    public function test_createWithConfig_NotAFactoryClass()
    {
        $ldap = Ldap::createWithConfig(array(), SomeClass::class);
    }

    public function test_getAdapter()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $this->assertSame($adapter, $ldap->getAdapter());
    }

    public function test_getBinding()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();
        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $this->assertSame($binding, $ldap->getBinding());
    }

    public function test_getEntryManager()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);
        $this->assertSame($em, $ldap->getEntryManager());
    }

    public function test_bind_WithoutArgs()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('bind')
                   ->with(null, null)
                   ->willReturn(true);

        $this->assertTrue($ldap->bind());
    }

    public function test_bind_WithoutPassword()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('bind')
                   ->with('dc=korowai,dc=org', null)
                   ->willReturn(true);

        $this->assertTrue($ldap->bind('dc=korowai,dc=org'));
    }

    public function test_bind()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('bind')
                   ->with('dc=korowai,dc=org', 's3cr3t')
                   ->willReturn(true);

        $this->assertTrue($ldap->bind('dc=korowai,dc=org', 's3cr3t'));
    }

    public function test_isBound_True()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('isBound')
                   ->with()
                   ->willReturn(true);

        $this->assertTrue($ldap->isBound());
    }

    public function test_isBound_False()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('isBound')
                   ->with()
                   ->willReturn(false);

        $this->assertFalse($ldap->isBound());
    }

    public function test_unbind()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $binding = $this->getBindingMock();

        $adapter->expects($this->once())
               ->method('getBinding')
               ->with()
               ->willReturn($binding);
        $adapter->expects($this->never())
               ->method('getEntryManager');

        $binding->expects($this->once())
                   ->method('unbind')
                   ->with()
                   ->willReturn(true);

        $this->assertTrue($ldap->unbind());
    }

    public function test_add()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();

        $adapter->expects($this->never())
               ->method('getBinding');
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);

        $entry = new Entry('dc=korowai,dc=org');
        $em->expects($this->once())
           ->method('add')
           ->with($entry)
           ->willReturn('ok');

        $this->assertSame('ok', $ldap->add($entry));
    }

    public function test_update()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();

        $adapter->expects($this->never())
               ->method('getBinding');
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);

        $entry = new Entry('dc=korowai,dc=org');
        $em->expects($this->once())
           ->method('update')
           ->with($entry)
           ->willReturn('ok');

        $this->assertSame('ok', $ldap->update($entry));
    }

    public function test_rename_WithoutDeleteOldRdn()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();

        $adapter->expects($this->never())
               ->method('getBinding');
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);

        $entry = new Entry('dc=korowai,dc=org');
        $em->expects($this->once())
           ->method('rename')
           ->with($entry, 'cn=korowai', true)
           ->willReturn('ok');

        $this->assertSame('ok', $ldap->rename($entry, 'cn=korowai'));
    }

    public function test_rename()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();

        $adapter->expects($this->never())
               ->method('getBinding');
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);

        $entry = new Entry('dc=korowai,dc=org');
        $em->expects($this->once())
           ->method('rename')
           ->with($entry, 'cn=korowai', false)
           ->willReturn('ok');

        $this->assertSame('ok', $ldap->rename($entry, 'cn=korowai', false));
    }

    public function test_delete()
    {
        $adapter = $this->getAdapterMock();
        $ldap = new Ldap($adapter);
        $em = $this->getEntryManagerMock();

        $adapter->expects($this->never())
               ->method('getBinding');
        $adapter->expects($this->once())
               ->method('getEntryManager')
               ->with()
               ->willReturn($em);

        $entry = new Entry('dc=korowai,dc=org');
        $em->expects($this->once())
           ->method('delete')
           ->with($entry)
           ->willReturn('ok');

        $this->assertSame('ok', $ldap->delete($entry));
    }

    public function test_createQuery_DefaultOptions()
    {
        $adapter = $this->getAdapterMock();
        $query = $this->getQueryMock();
        $ldap = new Ldap($adapter);

        $adapter->expects($this->once())
                ->method('createQuery')
                ->with('dc=example,dc=org', 'objectClass=*', array())
                ->willReturn($query);

        $this->assertEquals($query, $ldap->createQuery('dc=example,dc=org', 'objectClass=*'));
    }

    public function test_createQuery_CustomOptions()
    {
        $adapter = $this->getAdapterMock();
        $query = $this->getQueryMock();
        $ldap = new Ldap($adapter);

        $adapter->expects($this->once())
                ->method('createQuery')
                ->with('dc=example,dc=org', 'objectClass=*', array('scope' => 'base'))
                ->willReturn($query);

        $this->assertEquals(
            $query,
            $ldap->createQuery('dc=example,dc=org', 'objectClass=*', array('scope' => 'base'))
        );
    }
}

// vim: syntax=php sw=4 ts=4 et:
