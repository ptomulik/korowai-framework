<?php
/**
 * @file src/Korowai/Lib/Basic/Singleton.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\ContextLib
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Basic;

/**
 * A trait for singleton classes.
 */
trait Singleton
{
    /**
     * Store the singleton object.
     *
     * @var object
     */
    private static $instance;


    /**
     * Singleton's constructor is hidden from user.
     */
    private function __construct()
    {
        $this->initializeSingleton();
    }

    /**
     * Singleton's __clone() is hidden from user.
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }

    /**
     * Singleton's __wakeup() is hidden from user.
     * @codeCoverageIgnore
     */
    private function __wakeup()
    {
    }

    /**
     * Initializes the object.
     *
     * This method may be overwriten in the target class.
     */
    protected function initializeSingleton()
    {
    }

    /**
     * Fetch an instance of the class.
     */
    public static function getInstance() : self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

// vim: syntax=php sw=4 ts=4 et:
