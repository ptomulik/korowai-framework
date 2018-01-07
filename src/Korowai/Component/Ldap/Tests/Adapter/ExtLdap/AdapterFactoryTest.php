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
use Korowai\Component\Ldap\Adapter\ExtLdap\Adapter;
use Korowai\Component\Ldap\Adapter\ExtLdap\AdapterFactory;
use Korowai\Component\Ldap\Adapter\AbstractAdapterFactory;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AdapterFactoryTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;

    public function getLdapFunctionMock(...$args)
    {
        return $this->getFunctionMock('\\Korowai\\Component\\Ldap\\Adapter\ExtLdap', ...$args);
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionMessage The LDAP PHP extension is not enabled
     * @expectedExceptionCode -1
     */
    public function test_construct_ExtLdapNotLoaded()
    {
        $this->getLdapFunctionMock('extension_loaded')
             ->expects($this->once())
             ->with('ldap')
             ->willReturn(false);
        new AdapterFactory();
    }


    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionMessage Error message
     * @expectedExceptionCode -1
     */
    public function test_createAdapter_ConnectFailure_1()
    {
        $this->getLdapFunctionMock("ldap_connect")
             ->expects($this->once())
             ->with('ldap://localhost')
             ->willReturnCallback(
                 function (...$args) {
                     trigger_error('Error message');
                 }
             );

        $this->getLdapFunctionMock("ldap_set_option")
             ->expects($this->never());

        $factory = new AdapterFactory;
        $factory->configure(array());
        $factory->createAdapter();
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionMessage Failed to create LDAP connection
     * @expectedExceptionCode -1
     */
    public function test_createAdapter_ConnectFailure_2()
    {
        $this->getLdapFunctionMock("ldap_connect")
             ->expects($this->once())
             ->with('ldap://localhost')
             ->willReturn(null);

        $this->getLdapFunctionMock("ldap_set_option")
             ->expects($this->never());

        $factory = new AdapterFactory;
        $factory->configure(array());
        $factory->createAdapter();
    }

    /**
     * @runInSeparateProcess
     * @expectedException \Korowai\Component\Ldap\Exception\LdapException
     * @expectedExceptionMessage Error message
     * @expectedExceptionCode 123
     */
    public function test_createAdapter_SetOptionFailure()
    {
        $this->getLdapFunctionMock("ldap_connect")
             ->expects($this->once())
             ->with('ldap://localhost')
             ->willReturnCallback(function(...$args) {
                 return \ldap_connect(...$args);
             });

        $this->getLdapFunctionMock("ldap_set_option")
             ->expects($this->once())
             ->willReturn(false);

        $this->getLdapFunctionMock("ldap_errno")
             ->expects($this->once())
             ->with($this->callback('is_resource'))
             ->willReturn(123);

        $this->getLdapFunctionMock("ldap_err2str")
             ->expects($this->once())
             ->with(123)
             ->willReturn("Error message");

        $factory = new AdapterFactory;
        $factory->configure(array());
        $factory->createAdapter();
    }

    public function test_createAdapter()
    {
        $factory = new AdapterFactory;
        $factory->configure(array());
        $adapter = $factory->createAdapter();
        $this->assertInstanceOf(Adapter::class, $adapter);
        $this->assertTrue($adapter->getLdapLink()->isValid());
    }

}

// vim: syntax=php sw=4 ts=4 et:
