<?php
/**
 * @file src/Korowai/Component/Ldap/Tests/Adapter/ExtLdap/ResultReferenceIteratorTest.php
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

use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReferenceIterator;
use Korowai\Component\Ldap\Adapter\ExtLdap\ResultReference;
use Korowai\Component\Ldap\Adapter\ExtLdap\Result;


/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ResultReferenceIteratorTest extends TestCase
{
    public function test_getResult()
    {
        $result = $this->createMock(Result::class);
        $ref = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref);
        $this->assertSame($result, $iterator->getResult());
    }

    public function test_getReference()
    {
        $result = $this->createMock(Result::class);
        $ref = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref);
        $this->assertSame($ref, $iterator->getReference());
    }

    public function test_current()
    {
        $result = $this->createMock(Result::class);
        $ref = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref);
        $this->assertSame($ref, $iterator->current());
    }

    public function test_key()
    {
        $result = $this->createMock(Result::class);
        $ref = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref);

        $ref->expects($this->once())
              ->method('getDn')
              ->with()
              ->willReturn('dc=korowai,dc=org');

        $this->assertEquals('dc=korowai,dc=org', $iterator->key());
    }

    public function test_next()
    {
        $result = $this->createMock(Result::class);
        $ref1 = $this->createMock(ResultReference::class);
        $ref2 = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref1);

        $this->assertSame($ref1, $iterator->getReference());

        $ref1->expects($this->once())
               ->method('next_reference')
               ->with()
               ->willReturn($ref2);
        $ref2->method('next_reference')
               ->willReturn(null);

        $iterator->next();
        $this->assertSame($ref2, $iterator->getReference());
        $iterator->next();
        $this->assertNull($iterator->getReference());
    }

    public function test_rewind()
    {
        $result = $this->createMock(Result::class);
        $ref1 = $this->createMock(ResultReference::class);
        $ref2 = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref2);

        $this->assertSame($ref2, $iterator->getReference());

        $result->expects($this->once())
               ->method('first_reference')
               ->with()
               ->willReturn($ref1);

        $this->assertSame($ref2, $iterator->getReference());
        $iterator->rewind();
        $this->assertSame($ref1, $iterator->getReference());
    }

    public function test_valid()
    {
        $result = $this->createMock(Result::class);
        $ref1 = $this->createMock(ResultReference::class);
        $ref2 = $this->createMock(ResultReference::class);
        $iterator = new ResultReferenceIterator($result, $ref1);

        $this->assertSame($ref1, $iterator->getReference());

        $ref1->expects($this->once())
               ->method('next_reference')
               ->with()
               ->willReturn($ref2);
        $ref2->method('next_reference')
               ->willReturn(null);

        $this->assertTrue($iterator->valid());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}

// vim: syntax=php sw=4 ts=4 et:
