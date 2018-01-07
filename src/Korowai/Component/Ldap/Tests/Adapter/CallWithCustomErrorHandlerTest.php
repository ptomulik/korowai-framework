<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Korowai\Component\Ldap\Adapter\CallWithCustomErrorHandler;

class CallWithCustomErrorHandlerTestException extends \Exception {};

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class CallWithCustomErrorHandlerTest extends TestCase
{
    use CallWithCustomErrorHandler;

    private function add(int $arg1, int $arg2)
    {
        trigger_error('error occurred');
        return $arg1 + $arg2;
    }

    private function raise()
    {
        throw new CallWithCustomErrorHandlerTestException('error occurred');
    }

    private function trigger_and_raise()
    {
        trigger_error('error occurred');
        throw new CallWithCustomErrorHandlerTestException('error occurred');
    }

    public function test_callWithCustomErrorHandler()
    {
        $error = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = "outer error handler: $errstr";
            return true;
        });
        $this->assertSame(123, $this->callWithCustomErrorHandler(
            function($errno, $errstr) use (&$error) {
                $error = "inner error handler: $errstr";
                return true;
            },
            'add', 100, 23)
        );
        $this->assertSame('inner error handler: error occurred', $error);

        // Error handler should be restored
        trigger_error("error occurred");
        $this->assertSame('outer error handler: error occurred', $error);
    }

    public function test_callWithCustomErrorHandler_Exception()
    {
        $error = null;
        $exception = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = "outer error handler: $errstr";
            return true;
        });
        try {
            $this->callWithCustomErrorHandler(
                function ($errno, $errstr) use (&$error) {
                    $error = "inner error handler: $errstr";
                    return true;
                },
                'raise'
            );
        } catch(CallWithCustomErrorHandlerTestException $e) {
            $exception = $e;
        }
        $this->assertNull($error);
        $this->assertNotNull($exception);

        // Error handler should be restored
        trigger_error("error occurred");
        $this->assertSame('outer error handler: error occurred', $error);
    }

    public function test_callWithCustomErrorHandler_ErrorAndException()
    {
        $error = null;
        $exception = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = "outer error handler: $errstr";
            return true;
        });
        try {
            $this->callWithCustomErrorHandler(
                function ($errno, $errstr) use (&$error) {
                    $error = "inner error handler: $errstr";
                    return true;
                },
                'trigger_and_raise'
            );
        } catch(CallWithCustomErrorHandlerTestException $e) {
            $exception = $e;
        }
        $this->assertSame('inner error handler: error occurred', $error);
        $this->assertNotNull($exception);

        // Error handler should be restored
        trigger_error("error occurred");
        $this->assertSame('outer error handler: error occurred', $error);
    }

    public function test_callWithCustomErrorHandler_HandlerThrownException()
    {
        $error = null;
        $exception = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = "outer error handler: $errstr";
            return true;
        });
        try {
            $this->callWithCustomErrorHandler(
                function ($errno, $errstr) use (&$error) {
                    $error = "inner error handler: $errstr";
                    throw new CallWithCustomErrorHandlerTestException();
                },
                'add', 100, 23
            );
        } catch(CallWithCustomErrorHandlerTestException $e) {
            $exception = $e;
        }
        $this->assertSame('inner error handler: error occurred', $error);
        $this->assertNotNull($exception);

        // Error handler should be restored
        trigger_error("error occurred");
        $this->assertSame('outer error handler: error occurred', $error);
    }
}

// vim: syntax=php sw=4 ts=4 et:
