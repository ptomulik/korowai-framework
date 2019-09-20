<?php
/**
 * @file src/Korowai/Lib/Context/Tests/AbstractManagedContextFactoryTest.php
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
use Korowai\Lib\Context\ContextManagerInterface;
use Korowai\Lib\Context\AbstractManagedContextFactory;
use Korowai\Lib\Context\ContextFactoryStackInterface;
use Korowai\Lib\Context\ContextFactoryStack;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class AbstractManagedContextFactoryTest extends TestCase
{
    public function test__implements__ContextFactoryInterface()
    {
        $interfaces = class_implements(AbstractManagedContextFactory::class);
        $this->assertContains(ContextFactoryInterface::class, $interfaces);
    }

    public function test__implements__ContextManagerInterface()
    {
        $interfaces = class_implements(AbstractManagedContextFactory::class);
        $this->assertContains(ContextManagerInterface::class, $interfaces);
    }

    public function test__enterContext_and_exitContext()
    {
        $factory = $this->getMockBuilder(AbstractManagedContextFactory::class)
                        ->getMockForAbstractClass();

        $stack = ContextFactoryStack::getInstance();
        $stack->clean();

        $this->assertSame($factory, $factory->enterContext());

        $this->assertEquals(1, $stack->size());
        $this->assertSame($factory, $stack->top());

        $this->assertFalse($factory->exitContext(null));
        $this->assertEquals(0, $stack->size());
    }
}

// vim: syntax=php sw=4 ts=4 et:
