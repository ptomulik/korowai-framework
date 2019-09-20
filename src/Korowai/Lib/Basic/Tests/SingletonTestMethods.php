<?php
/**
 * @file src/Korowai/Lib/Basic/Tests/SingletonTestMethods.php
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
trait SingletonTestMethods
{
    use SingletonTestHelpers;

    public function test__Singleton__getInstance()
    {
        $this->check__Singleton__getInstance($this->getClassUnderTest());
    }

    public function test__Singleton__construct()
    {
        $this->check__Singleton__construct($this->getClassUnderTest());
    }

    public function test__Singleton__clone()
    {
        $this->check__Singleton__clone($this->getClassUnderTest());
    }

    public function test__Singleton__wakeup()
    {
        $this->check__Singleton__wakeup($this->getClassUnderTest());
    }
}

// vim: syntax=php sw=4 ts=4 et:
