<?php
/**
 * @file src/Korowai/Lib/Basic/Tests/SingletonTestHelpers.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldif
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Basic\Tests;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
trait SingletonTestHelpers
{
    protected static function getPrivateErrorRegExp(string $method)
    {
        return '/Call to private (?:method )?' . preg_quote($method) . '/';
    }

    public function check__Singleton__getInstance(string $class)
    {
        $obj1 = $class::getInstance();
        $obj2 = $class::getInstance();
        $this->assertSame($obj1, $obj2);
    }

    public function check__Singleton__construct(string $class)
    {
        $regex = self::getPrivateErrorRegExp($class . '::__construct()');
        $this->expectException(\Error::class);
        $this->expectExceptionMessageRegExp($regex);

        new $class();
    }

    public function check__Singleton__clone(string $class)
    {
        $obj = $class::getInstance();

        $regex = self::getPrivateErrorRegExp(get_class($obj) . '::__clone()');

        $this->expectException(\Error::class);
        $this->expectExceptionMessageRegExp($regex);

        $obj->__clone();
    }

    public function check__Singleton__wakeup(string $class)
    {
        $obj = $class::getInstance();

        $regex = self::getPrivateErrorRegExp(get_class($obj) . '::__wakeup()');

        $this->expectException(\Error::class);
        $this->expectExceptionMessageRegExp($regex);

        $obj->__wakeup();
    }
}

// vim: syntax=php sw=4 ts=4 et:
