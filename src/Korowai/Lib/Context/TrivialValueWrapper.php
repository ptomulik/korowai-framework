<?php
/**
 * @file src/Korowai/Lib/Context/TrivialValueWrapper.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Contextlib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context;

/**
 * A trivial context manager which only wraps a single value.
 *
 * The enterContext() method returns the ``$value`` passed as argument to
 * ``__construct()``, while exitContext() returns false.
 */
class TrivialValueWrapper implements ContextManagerInterface
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Initializes the object.
     *
     * @param mixed $value The value being wrapped by the object.
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the value provided to constructor.
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * {@inheritdoc}
     */
    public function enterContext()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function exitContext(?\Throwable $exception = null) : bool
    {
        return false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
