<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/AbstractAdapterFactoryTest.php
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
use Korowai\Component\Ldap\Adapter\AbstractAdapterFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractAdapterFactoryTest extends TestCase
{
    public static function getDefaultConfig()
    {
        return array(
            'host' => 'localhost',
            'uri'  => 'ldap://localhost',
            'port' => 389,
            'encryption' => 'none',
            'options' => array()
        );
    }

    private function getAbstractAdapterFactoryMock($ctor = true, array $methods = array())
    {
        $builder = $this->getMockBuilder(AbstractAdapterFactory::class);

        if(!$ctor) {
            $builder->disableOriginalConstructor();
        } elseif(is_array($ctor)) {
            $builder->setConstructorArgs($ctor);
        }

        if(!in_array('configureNestedOptionsResolver', $methods)) {
            $methods[] = 'configureNestedOptionsResolver';
        }
        $builder->setMethods($methods);
        return $builder->getMockForAbstractClass();
    }

    public function test_configure_CtorWithConfig()
    {
        $config = array('host' => 'korowai.org');
        $factory = $this->getAbstractAdapterFactoryMock(false, array('configure'));
        $factory->expects($this->once())
                ->method('configure')
                ->with($config);

        $factory->__construct($config);
    }

    public function test_configure_ConfigureResolvers()
    {
        $resolver = null;
        $nestedResolver = null;

        $factory = $this->getAbstractAdapterFactoryMock(
            true,
            array('configureOptionsResolver')
        );

        $factory->expects($this->once())
                ->method('configureOptionsResolver')
                ->with($this->isInstanceOf(OptionsResolver::class))
                ->willReturnCallback(function(OptionsResolver $r) use (&$resolver) {
                    $resolver = $r;
                });

        $factory->expects($this->once())
                ->method('configureNestedOptionsResolver')
                ->with($this->isInstanceOf(OptionsResolver::class))
                ->willReturnCallback(function(OptionsResolver $r) use (&$nestedResolver) {
                    $nestedResolver = $r;
                });

        $factory->configure(array());
        $expected = array('options' => array());
        $this->assertInstanceOf(OptionsResolver::class, $resolver);
        $this->assertInstanceOf(OptionsResolver::class, $nestedResolver);
        $this->assertNotSame($resolver, $nestedResolver);
        $this->assertEquals($expected, $factory->getConfig());
    }

    public function test_configure_Defaults()
    {
        $factory = $this->getAbstractAdapterFactoryMock();

        $factory->configure(array());

        $expected = $this->getDefaultConfig();
        $this->assertEquals($expected, $factory->getConfig());
    }

    public function test_configure_Host()
    {
        $factory = $this->getAbstractAdapterFactoryMock();

        $factory->configure(array('host' => 'korowai.org'));

        $expected = $this->getDefaultConfig();
        $expected['host'] = 'korowai.org';
        $expected['uri'] = 'ldap://korowai.org';
        $this->assertEquals($expected, $factory->getConfig());
    }

    public function test_configure_HostEncryption()
    {
        $factory = $this->getAbstractAdapterFactoryMock();

        $factory->configure(array('host' => 'korowai.org', 'encryption' => 'ssl'));

        $expected = $this->getDefaultConfig();
        $expected['host'] = 'korowai.org';
        $expected['encryption'] = 'ssl';
        $expected['uri'] = 'ldaps://korowai.org';
        $expected['port'] = 636;
        $this->assertEquals($expected, $factory->getConfig());
    }

    public function test_configure_HostEncryptionPort()
    {
        $factory = $this->getAbstractAdapterFactoryMock();

        $factory->configure(array('host' => 'korowai.org', 'encryption' => 'ssl', 'port' => 123));

        $expected = $this->getDefaultConfig();
        $expected['host'] = 'korowai.org';
        $expected['encryption'] = 'ssl';
        $expected['uri'] = 'ldaps://korowai.org:123';
        $expected['port'] = 123;
        $this->assertEquals($expected, $factory->getConfig());
    }

    public function test_configure_NestedOptions()
    {
        $factory = $this->getAbstractAdapterFactoryMock();

        $factory->expects($this->once())
                ->method('configureNestedOptionsResolver')
                ->willReturnCallback(function($resolver) {
                    $resolver->setDefault('protocol_version', 3);
                });

        $factory->configure(array());

        $expected = $this->getDefaultConfig();
        $expected['options']['protocol_version'] = 3;
        $this->assertEquals($expected, $factory->getConfig());
    }
}

// vim: syntax=php sw=4 ts=4 et:
