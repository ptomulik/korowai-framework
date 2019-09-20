<?php
/**
 * @file src/Korowai/Lib/Error/AbstractManagedErrorHandler.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ErrorLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Error;

use Korowai\Lib\Context\ContextManagerInterface;

/**
 * Abstract base class for context-managed error handlers.
 *
 * The base class implements enterContext() and exitContext(). The user has to
 * implement __invoke() method as defined in ErrorHandlerInterface.
 */
abstract class AbstractManagedErrorHandler implements ErrorHandlerInterface, ContextManagerInterface
{
    /**
     * @var int
     */
    protected $errorTypes;

    /**
     * Initializes the object
     *
     * @param int $errorTypes
     *        Can be used to mask the triggering of the error handler.
     */
    public function __construct(int $errorTypes = E_ALL | E_STRICT)
    {
        $this->errorTypes = $errorTypes;
    }

    /**
     * Returns the integer defining error types that are captured by the error
     * handler.
     *
     * @return int
     */
    public function getErrorTypes() : int
    {
        return $this->errorTypes;
    }

    /**
     * Sets this error handler object as error handler using PHP function
     * ``set_error_handler()`` and returns ``$this``.
     *
     * @return AbstractManagedErrorHandler
     */
    public function enterContext()
    {
        set_error_handler($this, $this->getErrorTypes());
        return $this;
    }

    /**
     * Restores original error handler using PHP function
     * \restore_error_hander() and returns ``false``.
     *
     * @return bool Always ``false``.
     */
    public function exitContext(?\Throwable $exception = null) : bool
    {
        restore_error_handler();
        return false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
