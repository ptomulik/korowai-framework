<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/AdapterTest.php
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
use Korowai\Component\Ldap\AbstractAdapter;
use Korowai\Component\Ldap\Adapter\ExtLdap\Adapter;
use Korowai\Component\Ldap\Adapter\ExtLdap\Binding;
use Korowai\Component\Ldap\Adapter\ExtLdap\EntryManager;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
use Korowai\Component\Ldap\Adapter\ExtLdap\Query;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AdapterTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    public function getLdapFunctionMock(...$args)
    {
        return $this->getFunctionMock('\\Korowai\\Component\\Ldap\\Adapter\ExtLdap', ...$args);
    }

    public function test_getBinding()
    {
        $link = $this->createMock(LdapLink::class);

        $adapter = new Adapter($link);

        $bind1 = $adapter->getBinding();
        $bind2 = $adapter->getBinding();
        $this->assertSame($bind1, $bind2);
        $this->assertInstanceOf(Binding::class, $bind1);
    }

    public function test_getEntryManager()
    {
        $link = $this->createMock(LdapLink::class);

        $adapter = new Adapter($link);

        $em1 = $adapter->getEntryManager();
        $em2 = $adapter->getEntryManager();
        $this->assertSame($em1, $em2);
        $this->assertInstanceOf(EntryManager::class, $em1);
    }

    public function test_createQuery()
    {
        $link = $this->createMock(LdapLink::class);

        $adapter = new Adapter($link);

        $query = $adapter->createQuery("dc=korowai,dc=org", "objectClass=*", array('scope' => 'one'));

        $this->assertInstanceOf(Query::class, $query);
        $this->assertEquals("dc=korowai,dc=org", $query->getBaseDn());
        $this->assertEquals("objectClass=*", $query->getFilter());
        $this->assertEquals('one', $query->getOptions()['scope']);
    }
}

// vim: syntax=php sw=4 ts=4 et:
