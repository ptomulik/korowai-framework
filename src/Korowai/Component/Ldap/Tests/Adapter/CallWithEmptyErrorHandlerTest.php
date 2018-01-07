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
use Korowai\Component\Ldap\Adapter\CallWithEmptyErrorHandler;

class CallWithEmptyErrorHandlerTestException extends \Exception {};

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class CallWithEmptyErrorHandlerTest extends TestCase
{
    use CallWithEmptyErrorHandler;

    private function add(int $arg1, int $arg2)
    {
        trigger_error('Error ocurred');
        return $arg1 + $arg2;
    }

    private function raise()
    {
        throw new CallWithEmptyErrorHandlerTestException('Error ocurred');
    }

    public function test_callWithEmptyErrorHandler()
    {
        $error = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = 'outer error handler';
            return true;
        });
        $this->assertSame(123, $this->callWithEmptyErrorHandler('add', 100, 23));
        $this->assertNull($error);

        // Error handler should be restored
        trigger_error("Error occurred");
        $this->assertSame('outer error handler', $error);
    }

    public function test_callWithEmptyErrorHandler_Exception()
    {
        $error = null;
        $exception = null;
        set_error_handler(function($errno, $errstr) use (&$error) {
            $error = 'outer error handler';
            return true;
        });
        try {
            $this->callWithEmptyErrorHandler('raise');
        } catch(CallWithEmptyErrorHandlerTestException $e) {
            $exception = $e;
        }
        $this->assertNull($error);
        $this->assertNotNull($exception);

        // Error handler should be restored
        trigger_error("Error occurred");
        $this->assertSame('outer error handler', $error);
    }
}

// vim: syntax=php sw=4 ts=4 et:
