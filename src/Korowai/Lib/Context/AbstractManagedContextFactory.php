<?php
/**
 * @file src/Korowai/Lib/Context/AbstractManagedContextFactory.php
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
 * Abstract base class for managed custom context factories.
 *
 * A managed context factory implements enterContext() and exitContext(), so it
 * also works as a context manager.
 */
abstract class AbstractManagedContextFactory implements ContextFactoryInterface, ContextManagerInterface
{
    /**
     * {@inheritdocs}
     */
    public function enterContext()
    {
        ContextFactoryStack::getInstance()->push($this);
        return $this;
    }

    /**
     * {@inheritdocs}
     */
    public function exitContext(?\Throwable $exception = null) : bool
    {
        ContextFactoryStack::getInstance()->pop();
        return false;
    }
}

// vim: syntax=php sw=4 ts=4 et:
