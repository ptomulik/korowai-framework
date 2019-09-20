<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultEntryTest.php
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

use Korowai\Component\Ldap\Adapter\ExtLdap\Result;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultAttributeIterator;
use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;


/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultEntryTest extends TestCase
{
    private function getResultMock(bool $withLink = true)
    {
        $result = $this->createMock(Result::class);
        if($withLink) {
            $link = $this->createMock(LdapLink::class);

            $result->method('getLink')
                   ->with()
                   ->willReturn($link);
        }
        return $result;
    }

    public function test_getResource()
    {
        $result = $this->getResultMock(false);
        $entry = new ResultEntry('ldap entry', $result);
        $this->assertSame('ldap entry', $entry->getResource());
    }

    public function test_getResult()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);
        $this->assertSame($result, $entry->getResult());
    }

    public function test_first_attribute()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('first_attribute')
               ->with($this->identicalTo($entry))
               ->willReturn('first attribute');
        $this->assertSame('first attribute', $entry->first_attribute());
    }

    public function test_get_attributes()
    {
        $attributes = array(
                 array('a1val1', 'a1val2'),
                 array('a2val1', 'a2val2'),
                 'count' => 2,
                 'foo' => 'bar'
        );
        $expected = array(
                 array('a1val1', 'a1val2'),
                 array('a2val1', 'a2val2'),
                 'count' => 2,
                 'foo' => 'bar'
        );
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_attributes')
               ->with($this->identicalTo($entry))
               ->willReturn($attributes);
        $this->assertSame($expected, $entry->get_attributes());
    }

    public function test_get_dn()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_dn')
               ->with($this->identicalTo($entry))
               ->willReturn('dc=korowai,dc=org');
        $this->assertSame('dc=korowai,dc=org', $entry->get_dn());
    }

    public function test_get_values_len()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_values_len')
               ->with($this->identicalTo($entry), 'userid')
               ->willReturn(1);
        $this->assertSame(1, $entry->get_values_len('userid'));
    }

    public function test_get_values()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_values')
               ->with($this->identicalTo($entry), 'userid')
               ->willReturn(array('ptomulik'));
        $this->assertSame(array('ptomulik'), $entry->get_values('userid'));
    }

    public function test_next_attribute()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('next_attribute')
               ->with($this->identicalTo($entry))
               ->willReturn('next attribute');
        $this->assertSame('next attribute', $entry->next_attribute());
    }

    public function test_next_entry()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('next_entry')
               ->with($this->identicalTo($entry))
               ->willReturn('next entry');
        $this->assertSame('next entry', $entry->next_entry());
    }

    public function test_getDn()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_dn')
               ->with($this->identicalTo($entry))
               ->willReturn('dc=korowai,dc=org');
        $this->assertSame('dc=korowai,dc=org', $entry->getDn());
    }

    public function test_getAttributes()
    {
        $attributes = array(
            'uid' => array(
                0 => 'korowai',
                'count' => 1
            ),
            'firstName' => array(
                0 => 'Old',
                'count' => 1

            ),
            'sn' => array (
                0 => 'Bro',
                1 => 'Foo',
                'count' => 2
            ),
            'count' => 3
        );
        $expected = array(
            'uid' => array('korowai'),
            'firstname' => array('Old'),
            'sn' => array('Bro', 'Foo')
        );

        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('get_attributes')
               ->with($this->identicalTo($entry))
               ->willReturn($attributes);

        $this->assertSame($expected, $entry->getAttributes());
    }

    public function test_getAttributeIterator()
    {
        $result = $this->getResultMock();
        $entry = new ResultEntry('ldap entry', $result);

        $result->getLink()
               ->expects($this->once())
               ->method('first_attribute')
               ->with($this->identicalTo($entry))
               ->willReturn('first attribute');

        $iterator = $entry->getAttributeIterator();
        $this->assertInstanceOf(ResultAttributeIterator::class, $iterator);

        $this->assertSame($entry, $iterator->getEntry());
        $this->assertEquals('first attribute', $iterator->getAttribute());

        $result->getLink()
               ->method('next_attribute')
               ->with($this->identicalTo($entry))
               ->willReturn('second attribute');

        $iterator->next();

        // single iterator instance per ResultEntry (dictated by ext-ldap implementation)
        $iterator2 = $entry->getAttributeIterator();
        $this->assertSame($iterator, $iterator2);
        $this->assertEquals('second attribute', $iterator->getAttribute());
    }

}

// vim: syntax=php sw=4 ts=4 et:
