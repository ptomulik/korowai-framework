<?php
/**
 * @file src/Korowai/Lib/Context/Tests/ContextFactoryStackTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldif
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context\Tests;

use PHPUnit\Framework\TestCase;

use Korowai\Lib\Context\ContextFactoryInterface;
use Korowai\Lib\Context\ContextFactoryStack;
use Korowai\Lib\Context\ContextFactoryStackInterface;
use Korowai\Lib\Context\ContextManagerInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class ContextFactoryStackTest extends TestCase
{
    use \Korowai\Lib\Basic\Tests\SingletonTestMethods;

    protected function getClassUnderTest()
    {
        return ContextFactoryStack::class;
    }

    protected function getDummyContextFactory()
    {
        return new class implements ContextFactoryInterface {
            public function getContextManager($arg) : ?ContextManagerInterface
            {
                return null;
            }
        };
    }

    protected function getDummyContextManager()
    {
        return new class implements ContextManagerInterface {
            public function enterContext()
            {
                return $this;
            }

            public function exitContext(?\Throwable $exception = null) : bool
            {
                return false;
            }
        };
    }

    public function test__implements__ContextFactoryStackInterface()
    {
        $interfaces = class_implements(ContextFactoryStack::class);
        $this->assertContains(ContextFactoryStackInterface::class, $interfaces);
    }

    public function test__implements__ContextFactoryInterface()
    {
        $interfaces = class_implements(ContextFactoryStack::class);
        $this->assertContains(ContextFactoryInterface::class, $interfaces);
    }

    public function test__basicStackMethods()
    {
        $f0 = $this->getDummyContextFactory();
        $f1 = $this->getDummyContextFactory();

        $stack = ContextFactoryStack::getInstance();
        $stack->clean();

        $this->assertEquals(0, $stack->size());
        $this->assertNull($stack->top());
        $this->assertNull($stack->pop());

        $stack->push($f0);
        $this->assertEquals(1, $stack->size());
        $this->assertSame($f0, $stack->top());
        $this->assertSame($f0, $stack->top()); // indempotent

        $stack->push($f1);
        $this->assertEquals(2, $stack->size());
        $this->assertSame($f1, $stack->top());
        $this->assertSame($f1, $stack->top()); // indempotent

        $this->assertSame($f1, $stack->pop());
        $this->assertEquals(1, $stack->size());

        $this->assertSame($f0, $stack->pop());
        $this->assertEquals(0, $stack->size());
    }

    public function test__getContextManager__onEmptyStack()
    {
        $stack = ContextFactoryStack::getInstance();
        $stack->clean();

        $this->assertNull($stack->getContextManager('an argument'));
    }

    public function test__getContextManager()
    {
        $cm0 = $this->getDummyContextManager();
        $cm1 = $this->getDummyContextManager();

        $f0 = $this->createMock(ContextFactoryInterface::class);
        $f1 = $this->createMock(ContextFactoryInterface::class);
        $f2 = $this->createMock(ContextFactoryInterface::class);

        $f0->expects($this->exactly(3))
           ->method('getContextManager')
           ->withConsecutive(['foo'], ['foo'], ['geez'])
           ->willReturn($cm0);
        $f1->expects($this->once())
           ->method('getContextManager')
           ->with('bar')
           ->willReturn($cm1);
        $f2->expects($this->once())
           ->method('getContextManager')
           ->with('geez')
           ->willReturn(null);

        $stack = ContextFactoryStack::getInstance();
        $stack->clean();

        $stack->push($f0);
        $this->assertSame($cm0, $stack->getContextManager('foo'));

        $stack->push($f1);
        $this->assertSame($cm1, $stack->getContextManager('bar'));

        $stack->pop();
        $this->assertSame($cm0, $stack->getContextManager('foo'));

        $stack->push($f2);
        $this->assertSame($cm0, $stack->getContextManager('geez'));
    }
}

// vim: syntax=php sw=4 ts=4 et:
