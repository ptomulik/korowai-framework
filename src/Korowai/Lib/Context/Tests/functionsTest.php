<?php
/**
 * @file src/Korowai/Lib/Context/Tests/functionsTest.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldif
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Lib\Context\Tests;

use PHPUnit\Framework\TestCase;

use function Korowai\Lib\Context\with;
use Korowai\Lib\Context\WithContextExecutor;
use Korowai\Lib\Context\TrivialValueWrapper;

/**
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 */
class function__with__Test extends TestCase
{
    public function test__withoutArgs()
    {
        $executor = with();
        $this->assertInstanceOf(WithContextExecutor::class, $executor);
        $this->assertEquals([], $executor->getContext());
    }

    public function test__withArgs()
    {
        $executor = with('foo', 'bar');
        $this->assertInstanceOf(WithContextExecutor::class, $executor);

        $context = $executor->getContext();

        $this->assertEquals(2, count($context));
        $this->assertInstanceOf(TrivialValueWrapper::class, $context[0]);
        $this->assertInstanceOf(TrivialValueWrapper::class, $context[1]);
        $this->assertEquals('foo', $context[0]->getValue());
        $this->assertEquals('bar', $context[1]->getValue());
    }
}

// vim: syntax=php sw=4 ts=4 et:
