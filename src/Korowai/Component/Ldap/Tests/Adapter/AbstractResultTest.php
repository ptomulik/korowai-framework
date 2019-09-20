<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/AbstractResultTest.php
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
use Korowai\Component\Ldap\Adapter\AbstractResult;
use Korowai\Component\Ldap\Adapter\ResultEntryInterface;
use Korowai\Component\Ldap\Adapter\ResultEntryIteratorInterface;

class _FakeEntry {
    private $dn;
    private $attribs;

    public function __construct($dn, $attribs) {
        $this->dn  = $dn;
        $this->attribs = $attribs;
    }
    public function getDn() { return $this->dn; }
    public function getAttributes() { return $this->attribs; }
}

class _FakeResultEntry {
    private $dn;
    private $attribs;

    public function __construct($dn, $attribs) {
        $this->dn  = $dn;
        $this->attribs = $attribs;
    }

    public function toEntry() {
        return new _FakeEntry($this->dn, $this->attribs);
    }
}

class _FakeResultEntryIterator implements ResultEntryIteratorInterface
{
    private $entries;

    public function __construct($entries) {
        $this->entries = $entries;
    }

    public function current() {
        return new _FakeResultEntry(key($this->entries), current($this->entries));
    }

    public function key() {
        return key($this->entries);
    }

    public function next() {
        next($this->entries);
    }

    public function rewind() {
        reset($this->entries);
    }

    public function valid() {
        return current($this->entries) !== false;
    }
}

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractResultTest extends TestCase
{
    private function getAbstractResultMock($ctor = true, array $methods = array())
    {
        $builder = $this->getMockBuilder(AbstractResult::class);

        if(!$ctor) {
            $builder->disableOriginalConstructor();
        } elseif(is_array($ctor)) {
            $builder->setConstructorArgs($ctor);
        }

        foreach(['getResultEntryIterator', 'getResultReferenceIterator'] as $method) {
            if(!in_array($method, $methods)) {
                $methods[] = $method;
            }
        }
        $builder->setMethods($methods);
        return $builder->getMockForAbstractClass();
    }

    public function test_getEntries()
    {
        $entries = array('k1' => 'e1', 'k2' => 'e2');
        $result = $this->getAbstractResultMock(true, array('getIterator'));
        $result->expects($this->once())
               ->method('getIterator')
               ->with()
               ->willReturn(new \ArrayIterator($entries));
        $this->assertEquals($entries, $result->getEntries());
    }

    public function test_getIterator()
    {
        $entries = array(
            'dc=korowai,dc=org' => array(
                'objectclass' => array(
                    'top', 'dcObject'
                ),
                'dc' => array(
                    'korowai'
                )
            ),
            'dc=sub,dc=korowai,dc=org' => array(
                'objectclass' => array(
                    'top', 'dcObject'
                ),
                'dc' => array(
                    'sub'
                )
            )
        );
        $result = $this->getAbstractResultMock();
        $result->expects($this->once())
               ->method('getResultEntryIterator')
               ->with()
               ->willReturn(new _FakeResultEntryIterator($entries));

        foreach($result->getIterator() as $dn => $entry) {
            $this->assertInstanceOf(_FakeEntry::class, $entry);
            $this->assertEquals($dn, $entry->getDn());
            $this->assertEquals($entries[$dn], $entry->getAttributes());
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
