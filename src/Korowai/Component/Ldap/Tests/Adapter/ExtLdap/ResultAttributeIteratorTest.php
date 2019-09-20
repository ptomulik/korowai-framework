<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultAttributeIteratorTest.php
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

use Korowai\Component\Ldap\Adapter\ExtLdap\ResultAttributeIterator;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;


/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultAttributeIteratorTest extends TestCase
{
    public function test_getEntry()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'attribName');
        $this->assertSame($entry, $iterator->getEntry());
    }

    public function test_getAttribute()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'attribName');
        $this->assertEquals('attribname', $iterator->getAttribute());
    }

    public function test_current()
    {
        $values = array('val1', 'val2', 'count' => 2);
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'attribName');
        $entry->expects($this->once())
              ->method('get_values')
              ->with('attribname')
              ->willReturn($values);
        $this->assertSame($values, $iterator->current());
    }

    public function test_key()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'attribName');
        $this->assertEquals('attribname', $iterator->key());
    }

    public function test_next()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'firstAttribute');

        $this->assertSame($entry, $iterator->getEntry());

        $entry->expects($this->once())
              ->method('next_attribute')
              ->with()
              ->willReturn('secondAttribute');

        $this->assertEquals('firstattribute', $iterator->getAttribute());
        $iterator->next();
        $this->assertEquals('secondattribute', $iterator->getAttribute());
    }

    public function test_rewind()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'secondAttribute');

        $entry->expects($this->once())
              ->method('first_attribute')
              ->with()
              ->willReturn('firstAttribute');

        $this->assertEquals('secondattribute', $iterator->key());
        $iterator->rewind();
        $this->assertEquals('firstattribute', $iterator->key());
    }

    public function test_valid()
    {
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultAttributeIterator($entry, 'firstAttribute');

        $entry->expects($this->once())
              ->method('next_attribute')
              ->with()
              ->willReturn(null);

        $this->assertTrue($iterator->valid());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}

// vim: syntax=php sw=4 ts=4 et:
