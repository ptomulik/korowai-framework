<?php
/**
 * @file src/Korowai/Lib/Context/WithContextExecutor.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ContextLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context;

/**
 * Executes user code within a predefined context.
 */
class WithContextExecutor implements ExecutorInterface
{
    /**
     * @var ContextManagerInterface[]
     */
    protected $context;

    /**
     * Initializes the object
     *
     * @param ContextManagerInterface[] $context
     */
    public function __construct(array $context)
    {
        $this->context = $context;
    }

    /**
     * Returns the context provided to __construct()
     *
     * @return array
     */
    public function getContext() : array
    {
        return $this->context;
    }

    /**
     * Calls user function within context.
     *
     * @param callable $func The user function to be called
     * @return mixed The value returned by ``$func``.
     */
    public function __invoke(callable $func)
    {
        $exception = null;
        $return = null;

        $i = 0;
        try {
            $args = $this->enterContext($i);
            $return = call_user_func_array($func, $args);
        } catch (\Throwable $e) {
            $exception = $e;
        }

        // exit all the entered contexts
        $exception = $this->exitContext($i, $exception);

        if (is_a($exception, \Throwable::class)) {
            throw $exception;
        }

        return $return;
    }

    /**
     * Invokes ``enterContext()`` method on the context managers from
     * ``$this->context`` array.
     *
     * @param int $i
     *          Index used by the internal loop (passed by reference, so
     *          its value is not lost when an exception is thrown).
     *
     * @return array
     *          An array of arguments to be passed to user function.
     */
    protected function enterContext(int &$i) : array
    {
        $args = [];
        for (; $i < count($this->context); $i++) {
            $args[] = $this->context[$i]->enterContext();
        }
        return $args;
    }

    /**
     * Invokes ``exitContext()`` method on the context managers from
     * ``$this->context`` array.
     *
     * @param int $i
     *          Index used by the internal loop (passed by reference, so its
     *          value is not lost when an exception is thrown).
     * @param \Throwable $exception
     *          An exception thrown from enterContext() or form user's
     *          callback.
     *
     * @return \Throwable
     *          An exception or null (if the exception was handled by one of
     *          the context managers).
     */
    protected function exitContext(int &$i, ?\Throwable $exception = null) : ?\Throwable
    {
        for ($i--; $i >= 0; $i--) {
            if ($this->context[$i]->exitContext($exception)) {
                $exception = null;
            }
        }
        return $exception;
    }
}

// vim: syntax=php sw=4 ts=4 et:
