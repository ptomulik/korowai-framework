<?php
/**
 * @file src/Korowai/Lib/Context/DefaultContextFactory.php
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
 * Default context factory.
 */
class DefaultContextFactory implements ContextFactoryInterface
{
    use \Korowai\Lib\Basic\Singleton;

    /**
     * {@inheritdoc}
     */
    public function getContextManager($arg) : ?ContextManagerInterface
    {
        if (is_a($arg, ContextManagerInterface::class)) {
            return $arg;
        } elseif (is_resource($arg)) {
            return new ResourceContextManager($arg);
        } else {
            return new TrivialValueWrapper($arg);
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
