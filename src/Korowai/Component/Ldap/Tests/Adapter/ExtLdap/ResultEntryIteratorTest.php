<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultEntryIteratorTest.php
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

use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntryIterator;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultEntry;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;


/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultEntryIteratorTest extends TestCase
{
    public function test_getResult()
    {
        $result = $this->createMock(Result::class);
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry);
        $this->assertSame($result, $iterator->getResult());
    }

    public function test_getEntry()
    {
        $result = $this->createMock(Result::class);
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry);
        $this->assertSame($entry, $iterator->getEntry());
    }

    public function test_current()
    {
        $result = $this->createMock(Result::class);
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry);
        $this->assertSame($entry, $iterator->current());
    }

    public function test_key()
    {
        $result = $this->createMock(Result::class);
        $entry = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry);

        $entry->expects($this->once())
              ->method('getDn')
              ->with()
              ->willReturn('dc=korowai,dc=org');

        $this->assertEquals('dc=korowai,dc=org', $iterator->key());
    }

    public function test_next()
    {
        $result = $this->createMock(Result::class);
        $entry1 = $this->createMock(ResultEntry::class);
        $entry2 = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry1);

        $this->assertSame($entry1, $iterator->getEntry());

        $entry1->expects($this->once())
               ->method('next_entry')
               ->with()
               ->willReturn($entry2);
        $entry2->method('next_entry')
               ->willReturn(null);

        $iterator->next();
        $this->assertSame($entry2, $iterator->getEntry());
        $iterator->next();
        $this->assertNull($iterator->getEntry());
    }

    public function test_rewind()
    {
        $result = $this->createMock(Result::class);
        $entry1 = $this->createMock(ResultEntry::class);
        $entry2 = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry2);

        $this->assertSame($entry2, $iterator->getEntry());

        $result->expects($this->once())
               ->method('first_entry')
               ->with()
               ->willReturn($entry1);

        $this->assertSame($entry2, $iterator->getEntry());
        $iterator->rewind();
        $this->assertSame($entry1, $iterator->getEntry());
    }

    public function test_valid()
    {
        $result = $this->createMock(Result::class);
        $entry1 = $this->createMock(ResultEntry::class);
        $entry2 = $this->createMock(ResultEntry::class);
        $iterator = new ResultEntryIterator($result, $entry1);

        $this->assertSame($entry1, $iterator->getEntry());

        $entry1->expects($this->once())
               ->method('next_entry')
               ->with()
               ->willReturn($entry2);
        $entry2->method('next_entry')
               ->willReturn(null);

        $this->assertTrue($iterator->valid());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}

// vim: syntax=php sw=4 ts=4 et:
