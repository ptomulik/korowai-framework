<?php
/**
 * @file src/Korowai/Lib/Context/Tests/DefaultContextFactoryTest.php
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

use Korowai\Lib\Context\DefaultContextFactory;
use Korowai\Lib\Context\ContextManagerInterface;
use Korowai\Lib\Context\ContextFactoryInterface;
use Korowai\Lib\Context\ResourceContextManager;
use Korowai\Lib\Context\TrivialValueWrapper;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class DefaultContextFactoryTest extends TestCase
{
    use \phpmock\phpunit\PHPMock;
    use \Korowai\Lib\Basic\Tests\SingletonTestMethods;

    protected function getClassUnderTest()
    {
        return DefaultContextFactory::class;
    }

    public function test__implements__ContextFactoryInterface()
    {
        $interfaces = class_implements(DefaultContextFactory::class);
        $this->assertContains(ContextFactoryInterface::class, $interfaces);
    }

    /**
     * @runInSeparateProcess
     */
    public function test__getContextManager__withContextManager()
    {
        $factory = DefaultContextFactory::getInstance();

        $is_resource = $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource');

        $is_resource->expects($this->never());

        $cm = $this->createMock(ContextManagerInterface::class);
        $cm->method('enterContext')->willReturn('foo');
        $cm->method('exitContext')->willReturn(false);

        $this->assertSame($cm, $factory->getContextManager($cm));
    }

    /**
     * @runInSeparateProcess
     */
    public function test__getContextManager__withResource()
    {
        $is_resource = $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource');

        $is_resource->expects($this->once())
                    ->with('foo')
                    ->willReturn(true);

        $factory = DefaultContextFactory::getInstance();

        $cm = $factory->getContextManager('foo');

        $this->assertInstanceOf(ResourceContextManager::class, $cm);
        $this->assertEquals('foo', $cm->getResource());
    }

    /**
     * @runInSeparateProcess
     */
    public function test__getContextManager__withValue()
    {
        $factory = DefaultContextFactory::getInstance();

        $is_resource = $this->getFunctionMock('Korowai\\Lib\\Context', 'is_resource');

        $is_resource->expects($this->once())
                    ->with('foo')
                    ->willReturn(false);

        $cm = $factory->getContextManager('foo');

        $this->assertInstanceOf(TrivialValueWrapper::class, $cm);
        $this->assertEquals('foo', $cm->getValue());
    }
}

// vim: syntax=php sw=4 ts=4 et:
