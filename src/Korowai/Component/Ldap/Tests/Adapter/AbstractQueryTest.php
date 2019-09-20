<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/AbstractQueryTest.php
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
use Korowai\Component\Ldap\Adapter\AbstractQuery;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractQueryTest extends TestCase
{
    public static function getDefaultOptions()
    {
        return array(
            'scope' => 'sub',
            'attributes' => '*',
            'attrsOnly' => 0,
            'deref' => 'never',
            'sizeLimit' => 0,
            'timeLimit' => 0
        );
    }

    public static function getDefaultOptionsResolved()
    {
        return array(
            'scope' => 'sub',
            'attributes' => array('*'),
            'attrsOnly' => 0,
            'deref' => 'never',
            'sizeLimit' => 0,
            'timeLimit' => 0
        );
    }

    private function getAbstractQueryMock($ctor = true, array $methods = array())
    {
        $builder = $this->getMockBuilder(AbstractQuery::class);

        if(!$ctor) {
            $builder->disableOriginalConstructor();
        } elseif(is_array($ctor)) {
            $builder->setConstructorArgs($ctor);
        }

        if(!in_array('doExecuteQuery', $methods)) {
            $methods[] = 'doExecuteQuery';
        }
        $builder->setMethods($methods);
        return $builder->getMockForAbstractClass();
    }

    public function test_getDefaultOptions()
    {
        $expected = static::getDefaultOptions();
        $this->assertEquals($expected, AbstractQuery::getDefaultOptions());
    }

    public function test_defaultOptions()
    {
        $query = $this->getAbstractQueryMock(array("dc=korowai,dc=org", "objectClass=*"));
        $expected = static::getDefaultOptionsResolved();
        $this->assertEquals($expected, $query->getOptions());
    }

    public function test_scope()
    {
        $scopes = array('base', 'one', 'sub');

        foreach($scopes as $scope) {
            $query = $this->getAbstractQueryMock(
                array("dc=korowai,dc=org", "objectClass=*",
                array('scope' => $scope))
            );
            $this->assertEquals($scope, $query->getOptions()['scope']);
        }
    }

    public function test_scope_Invalid()
    {
        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "scope" with value "foo" is invalid.');
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*",
            array('scope' => 'foo'))
        );
    }

    public function test_deref()
    {
        $scopes = array('always', 'never', 'finding', 'searching');

        foreach($scopes as $deref) {
            $query = $this->getAbstractQueryMock(
                array("dc=korowai,dc=org", "objectClass=*", 
                array('deref' => $deref))
            );
            $this->assertEquals($deref, $query->getOptions()['deref']);
        }
    }

    public function test_deref_Invalid()
    {
        $this->expectException(\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "deref" with value "foo" is invalid.');
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*",
            array('deref' => 'foo'))
        );
    }

    public function test_attributes()
    {
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*",
            array('attributes' => 'foo'))
        );
        $this->assertEquals(array('foo'), $query->getOptions()['attributes']);
    }

    public function test_getBaseDn()
    {
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*")
        );

        $this->assertEquals("dc=korowai,dc=org",  $query->getBaseDn());
    }

    public function test_getFilter()
    {
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*")
        );
        $this->assertEquals("objectClass=*",  $query->getFilter());
    }

    public function test_getResult()
    {
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*")
        );
        $query->expects($this->once())
              ->method('doExecuteQuery')
              ->with(); // XXX: ResultInterface mock not necessary?
        $query->getResult();
        $query->getResult();
    }

    public function test_execute()
    {
        $query = $this->getAbstractQueryMock(
            array("dc=korowai,dc=org", "objectClass=*")
        );
        $query->expects($this->exactly(2))
              ->method('doExecuteQuery')
              ->with(); // XXX: ResultInterface mock not necessary?
        $query->execute();
        $query->execute();
    }
}

// vim: syntax=php sw=4 ts=4 et:
