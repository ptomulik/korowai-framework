<?php
/**
 * This file is part of the Korowai package
 *
 * (c) Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 *
 * Distributed under MIT license.
 */

declare(strict_types=1);
namespace Korowai\Component\Ldap\Tests;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Entry;
use Korowai\Component\Ldap\Exception\AttributeException;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class EntryTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function test_constructNoDn()
    {
        new Entry();
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::__construct\(\) .+ integer given/
     */
    public function test_construct_InvalidDn()
    {
        new Entry(123);
    }

    public function test_construct_DefaultAttributes()
    {
        $entry = new Entry('dc=example,dc=com');
        $this->assertSame('dc=example,dc=com', $entry->getDn());
        $this->assertSame(array(), $entry->getAttributes());
    }

    public function test_construct_1()
    {
        $entry = new Entry('dc=example,dc=com', array());
        $this->assertSame('dc=example,dc=com', $entry->getDn());
        $this->assertSame(array(), $entry->getAttributes());
    }

    public function test_construct_2()
    {
        $entry = new Entry('dc=example,dc=com', array('userid' => array('ptomulik')));
        $this->assertSame('dc=example,dc=com', $entry->getDn());
        $this->assertSame(array('userid' => array('ptomulik')), $entry->getAttributes());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 2 .+::__construct\(\) .+ string given/
     */
    public function test_construct_InvalidAttributes_1()
    {
        new Entry('dc=example,dc=com', 'foo');
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::validateAttribute\(\) .+ integer given/
     */
    public function test_construct_InvalidAttributes_2()
    {
        new Entry('dc=example,dc=com', ['foo']);
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 2 .+::validateAttribute\(\) .+ string given/
     */
    public function test_construct_InvalidAttributes_3()
    {
        new Entry('dc=example,dc=com', ['foo' => 'bar']);
    }

    public function test_setDn()
    {
        $entry = new Entry('dc=example,dc=com');
        $this->assertSame('dc=example,dc=com', $entry->getDn());
        $entry->setDn('dc=korowai,dc=org');
        $this->assertSame('dc=korowai,dc=org', $entry->getDn());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::setDn\(\) .+ integer given/
     */
    public function test_setDn_InvalidDn()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setDn(123);
    }

    public function test_validateDn_Valid()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->validateDn('dc=korowai,dc=org');
        $this->assertSame('dc=example,dc=com', $entry->getDn());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::validateDn\(\) .+ integer given/
     */
    public function test_validateDn_Invalid()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->validateDn(123);
    }

    /**
     * @expectedException Korowai\Component\Ldap\Exception\AttributeException
     * @expectedExceptionMessage Entry 'dc=example,dc=com' has no attribute 'userid'
     */
    public function test_getAttribute_Inexistent()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->getAttribute('userid');
    }

    public function test_getAttribute_Existent()
    {
        $entry = new Entry('dc=example,dc=com', array('userid' => array('ptomulik')));
        $this->assertSame(array('ptomulik'), $entry->getAttribute('userid'));
    }

    public function test_hasAttribute_Inexistent()
    {
        $entry = new Entry('dc=example,dc=com');
        $this->assertFalse($entry->hasAttribute('userid'));
    }

    public function test_hasAttribute_Existent()
    {
        $entry = new Entry('dc=example,dc=com', array( 'userid' => array('ptomulik') ));
        $this->assertTrue($entry->hasAttribute('userid'));
        $this->assertFalse($entry->hasAttribute('userpassword'));
    }

    public function test_setAttributes_1()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttributes(array('userid' => array('ptomulik'), 'userpassword' => array('secret')));
        $this->assertSame(array('userid' => array('ptomulik'), 'userpassword' => array('secret')), $entry->getAttributes());
    }

    public function test_setAttributes_2()
    {
        $initial = array('userid' => array('ptomulik'), 'userpassword' => array('secret'));
        $extra = array('description' => array('Some text'));
        $final = $initial + $extra;
        $entry = new Entry('dc=example,dc=com', $initial);
        $entry->setAttributes($extra);
        $this->assertSame($final, $entry->getAttributes());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::setAttributes\(\) .+ string given/
     */
    public function test_setAttributes_Invalid_1()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttributes('userid');
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::validateAttribute\(\) .+ integer given/
     */
    public function test_setAttributes_Invalid_2()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttributes(array('userid'));
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 2 .+::validateAttribute\(\) .+ string given/
     */
    public function test_setAttributes_Invalid_3()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttributes(array('userid' => 'ptomulik'));
    }

    public function test_setAttribute()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttribute('userid', array('ptomulik'));
        $this->assertSame(array('userid' => array('ptomulik')), $entry->getAttributes());
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 1 .+::setAttribute\(\) .+ integer given/
     */
    public function test_setAttribute_Invalid_1()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttribute(123, array('ptomulik'));
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 2 .+::setAttribute\(\) .+ integer given/
     */
    public function test_setAttribute_Invalid_2()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttribute('userid', 123);
    }

    /**
     * @expectedException \TypeError
     * @expectedExceptionMessageRegExp /Argument 2 .+::setAttribute\(\) .+ string given/
     */
    public function test_setAttribute_Invalid_3()
    {
        $entry = new Entry('dc=example,dc=com');
        $entry->setAttribute('userid', 'ptomulik');
    }

    public function test_setAttribute_Invalid_4()
    {
        $attrs = array('userid' => array('ptomulik'));
        $entry = new Entry('dc=example,dc=com', $attrs);
        try {
            // one attribute (userpassword) is valid, but another (description) is invalid
            $entry->setAttributes(array('userpassword' => array('secret'), 'descrition' => 'failure'));
        } catch(\TypeError $e) {
        }
        // the entry must be left unchanged
        $this->assertSame($attrs, $entry->getAttributes());
    }
}

// vim: syntax=php sw=4 ts=4 et:
