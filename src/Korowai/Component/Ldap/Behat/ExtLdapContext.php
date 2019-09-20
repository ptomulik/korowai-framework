<?php
/**
 * @file src/Korowai/Component/Ldap/Behat/ExtLdapContext.php
 *
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
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AftereScenarioScope;

use Korowai\Component\Ldap\Ldap;
use Korowai\Component\Ldap\Exception\LdapException;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class ExtLdapContext implements Context
{
    use LdapHelper, CommonHelpers;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
        $this->initLdapHelper();
    }

    /**
     * Clear and initialize data in the ldap database.
     *
     * @BeforeScenario @initDbBeforeScenario
     * @BeforeSuite @initDbBeforeSuite
     * @BeforeFeature @initDbBeforeFeature
     * @AfterScenario @initDbAfterScenario
     * @AfterSuite @initDbAfterSuite
     * @AfterFeature @initDbAfterFeature
     */
    public static function initDb()
    {
        $db = LdapService::getInstance();
        $db->deleteAllData();
        $db->addFromLdifFile(__DIR__.'/../Resources/ldif/bootstrap.ldif');
    }

    public function decodeJsonPyStringNode(PyStringNode $pystring)
    {
        return json_decode($pystring->getRaw(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @Transform :config
     * @Transform :options
     */
    public function decodeJsonString($string)
    {
        return json_decode($string, true, 512, JSON_THROW_ON_ERROR);
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
        $expected_entries = $this->decodeJsonPyStringNode($pystring);
        $actual_entries = array_map(
            function ($e) {
                return $e->getAttributes();
            },
            $this->lastResult()->getEntries()
        );

        # handle passwords
        foreach ($expected_entries as $dn => $ee) {
            $expected_password = $ee['userpassword'][0] ?? null;
            $actual_password = $actual_entries[$dn]['userpassword'][0] ?? null;
            if (is_string($expected_password) && is_string($actual_password)) {
                $encrypted = self::encryptForComparison($expected_password, $actual_password);
                $expected_entries[$dn]['userpassword'][0] = $encrypted;
            }
        }
        Assert::assertEquals($expected_entries, $actual_entries);
    }
}

// vim: syntax=php sw=4 ts=4 et:
