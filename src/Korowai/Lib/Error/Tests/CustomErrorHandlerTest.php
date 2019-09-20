<?php
/**
 * @file src/Korowai/Lib/Error/Tests/CustomErrorHandlerTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldif
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Error\Tests;

use PHPUnit\Framework\TestCase;

use Korowai\Lib\Error\CustomErrorHandler;
use Korowai\Lib\Error\AbstractManagedErrorHandler;
use Korowai\Lib\Error\ErrorHandlerInterface;
use Korowai\Lib\Context\ContextManagerInterface;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class CustomErrorHandlerTest extends TestCase
{
    public function test__extends__AbstractManagedErrorHandler()
    {
        $parents = class_parents(CustomErrorHandler::class);
        $this->assertContains(AbstractManagedErrorHandler::class, $parents);
    }

    public function test__construct__withoutErrorTypes()
    {
        $func = function () {};
        $handler = new CustomErrorHandler($func);
        $this->assertSame($func, $handler->getErrorHandler());
        $this->assertEquals(E_ALL | E_STRICT,  $handler->getErrorTypes());
    }

    public function test__construct__withErrorTypes()
    {
        $func = function () {};
        $handler = new CustomErrorHandler($func, 123);

        $this->assertSame($func, $handler->getErrorHandler());
        $this->assertEquals(123,  $handler->getErrorTypes());
    }

    public function test__invoke()
    {
        $called = 0;
        $args = [];

        $handler = new CustomErrorHandler(
            function (int $severity, string $message, string $file, int $line)
            use (&$called, &$args) : bool {
                $called += 1;
                $args = func_get_args();
                return true;
            }
        );

        $this->assertTrue($handler(123, 'msg', 'foo.php', 456));
        $this->assertEquals(1, $called);
        $this->assertEquals([123, 'msg', 'foo.php', 456], $args);
    }
}

// vim: syntax=php sw=4 ts=4 et:
