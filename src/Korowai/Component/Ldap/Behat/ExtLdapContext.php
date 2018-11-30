<?php
/**
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package Korowai\Ldap
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Korowai\Component\Ldap\Behat;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Korowai\Component\Ldap\Ldap;
use Korowai\Component\Ldap\Exception\LdapException;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class ExtLdapContext implements Context
{
    private $ldap;
    private $exceptions;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->resetExceptions();
        $this->resetQueryResults();
    }

    protected function resetExceptions()
    {
        $this->exceptions = array();
    }

    protected function resetQueryResults()
    {
        $this->results = array();
    }

    protected function appendException($e)
    {
        $this->exceptions[] = $e;
    }

    protected function appendResult($result)
    {
        $this->results[] = $result;
    }

    protected function lastException()
    {
        if (count($this->exceptions) < 1) {
            return null;
        } else {
            return $this->exceptions[count($this->exceptions)-1];
        }
    }

    protected function lastResult()
    {
        if (count($this->results) < 1) {
            return null;
        } else {
            return $this->results[count($this->results)-1];
        }
    }

    protected function createLdapLinkWithConfig($config)
    {
        try {
            $this->ldap = Ldap::createWithConfig($config);
        } catch (\Exception $e) {
            $this->appendException($e);
        }
    }

    protected function bindWithArgs(...$args)
    {
        try {
            return $this->ldap->bind(...$args);
        } catch (\Exception $e) {
            $this->appendException($e);
            return false;
        }
    }

    protected function queryWithArgs(...$args)
    {
        try {
            $result = $this->ldap->query(...$args);
        } catch (\Exception $e) {
            $this->appendException($e);
            return false;
        }
        $this->appendResult($result);
        return $result;
    }

    public function decodeJsonPyStringNode(PyStringNode $pystring)
    {
        return json_decode($pystring->getRaw(), true);
    }

    /**
     * @Transform :config
     * @Transform :options
     */
    public function decodeJsonString($string)
    {
        return json_decode($string, true);
    }

    /**
     * @Given I am disconnected
     */
    public function iAmDisconnected()
    {
        if (isset($this->ldap)) {
            unset($this->ldap);
        }
    }

    /**
     * @Given I am connected to uri :uri
     */
    public function iAmConnectedToUri($uri)
    {
        $config = array('uri' => $uri);
        try {
            $this->ldap = Ldap::createWithConfig($config);
        } catch (\Exception $e) {
            $this->appendException($e);
        }
    }

    /**
     * @Given I am connected using config :config
     */
    public function iAmConnectedUsingConfig($config)
    {
        return $this->createLdapLinkWithConfig($config);
    }

    /**
     * @Given I am bound without arguments
     */
    public function iAmBoundWithoutArguments()
    {
        return $this->bindWithArgs();
    }

    /**
     * @Given I am bound with binddn :binddn
     */
    public function iAmBoundWithBindDn($binddn)
    {
        return $this->bindWithArgs($binddn);
    }

    /**
     * @Given I am bound with binddn :binddn and password :password
     */
    public function iAmBoundWithBindDnAndPassword($binddn, $password)
    {
        return $this->bindWithArgs($binddn, $password);
    }

    /**
     * @When I create ldap link with config :config
     */
    public function iCreateLdapLinkWithJsonConfig($config)
    {
        return $this->createLdapLinkWithConfig($config);
    }

    /**
     * @When I bind without arguments
     */
    public function iBindWithoutArguments()
    {
        return $this->bindWithArgs();
    }

    /**
     * @When I bind with binddn :binddn
     */
    public function iBindWithBindDn($binddn)
    {
        return $this->bindWithArgs($binddn);
    }

    /**
     * @When I bind with binddn :binddn and password :password
     */
    public function iBindWithBindDnAndPassword($binddn, $password)
    {
        return $this->bindWithArgs($binddn, $password);
    }

    /**
     * @When I query with basedn :basedn and filter :filter
     */
    public function iQueryWithBaseDnAndFilter($basedn, $filter)
    {
        return $this->queryWithArgs($basedn, $filter);
    }

    /**
     * @When I query with basedn :basedn, filter :filter and options :options
     */
    public function iQueryWithBaseDnFilterAndOptions($basedn, $filter, $options)
    {
        return $this->queryWithArgs($basedn, $filter, $options);
    }

    /**
     * @Then I should be bound
     */
    public function iShouldBeBound()
    {
        Assert::assertSame(true, $this->ldap->isBound());
    }

    /**
     * @Then I should see ldap exception with message :arg1
     */
    public function iShouldSeeLdapExceptionWithMessage($arg1)
    {
        $matchedExceptions = array_filter($this->exceptions, function ($e) use ($arg1) {
            return ($e instanceof LdapException) && $e->getMessage() == $arg1;
        });
        $expectedException = LdapException::class . '("' . $arg1 .'")';
        $foundExceptions = array_map(
            function ($e) {
                return get_class($e) . '("' . $e->getMessage() . '")';
            },
            $this->exceptions
        );
        $foundExceptionsStr = '[ ' . implode(', ', $foundExceptions) . ' ]';
        Assert::assertTrue(count($matchedExceptions) > 0, $expectedException . " not found in " . $foundExceptionsStr);
    }

    /**
     * @Then I should see ldap exception with code :arg1
     */
    public function iShouldSeeLdapExceptionWithCode($arg1)
    {
        Assert::assertInstanceOf(LdapException::class, $this->lastException());
        Assert::assertEquals($arg1, $this->lastException()->getCode());
    }

    /**
     * @Then I should see invalid options exception with message :arg1
     */
    public function iShouldSeeInvalidOptionsExceptionWithMessage($arg1)
    {
        Assert::assertInstanceOf(InvalidOptionsException::class, $this->lastException());
        Assert::assertEquals($arg1, $this->lastException()->getMessage());
    }

    /**
     * @Then I should see no exception
     */
    public function iShouldSeeNoException()
    {
        $e = $this->lastException();
        $msg = $e === null ? '' : "The last exception's message was: " . $e->getMessage();
        Assert::assertSame($e, null, $msg);
    }

    /**
     * @Then I should have a valid LDAP link
     */
    public function iShouldHaveAValidLdapLink()
    {
        Assert::assertInstanceOf(Ldap::class, $this->ldap);
        Assert::assertTrue($this->ldap->getBinding()->isLinkValid());
    }

    /**
     * @Then I should have no valid LDAP link
     */
    public function iShouldHaveNoValidLdapLink()
    {
        Assert::assertNull($this->ldap);
    }

    /**
     * @Then I should not be bound
     */
    public function iShouldNotBeBound()
    {
        Assert::assertFalse($this->ldap->isBound());
    }

    /**
     * @Then I should have last result entries
     */
    public function iShouldHaveLastResultEntries(PyStringNode $pystring)
    {
        $entries = $this->decodeJsonPyStringNode($pystring);
        $actual = array_map(
            function ($e) {
                return $e->getAttributes();
            },
            $this->lastResult()->getEntries()
        );
        Assert::assertEquals($entries, $actual);
    }
}

// vim: syntax=php sw=4 ts=4 et:
