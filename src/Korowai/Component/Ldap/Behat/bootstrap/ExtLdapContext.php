<?php

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
    }

    protected function resetExceptions()
    {
      $this->exceptions = array();
    }

    protected function appendException($e)
    {
      $this->exceptions[] = $e;
    }

    protected function lastException()
    {
      if(count($this->exceptions) < 1) {
        return null;
      } else {
        return $this->exceptions[count($this->exceptions)-1];
      }
    }

    /**
     * @Given I am disconnected
     */
    public function iAmDisconnected()
    {
      if(isset($this->ldap)) {
        unset($this->ldap);
      }
    }

    /**
     * @Given I am connected to uri :arg1
     */
    public function iAmConnectedToUri($arg1)
    {
      $config = array('uri' => $arg1);
      try {
        $this->ldap = Ldap::createWithConfig($config);
      } catch(\Exception $e) {
        $this->appendException($e);
      }
    }

    /**
     * @Given I am connected using JSON config :arg1
     */
    public function iAmConnectedUsingJsonConfig($arg1)
    {
      return $this->createLdapLinkWithJsonConfig($arg1);
    }

    /**
     * @When I create ldap link with JSON config :arg1
     */
    public function iCreateLdapLinkWithJsonConfig($arg1)
    {
      return $this->createLdapLinkWithJsonConfig($arg1);
    }

    protected function createLdapLinkWithJsonConfig($json_config)
    {
      $config = json_decode($json_config, true);
      try {
        $this->ldap = Ldap::createWithConfig($config);
      } catch (\Exception $e) {
        $this->appendException($e);
      }
    }


    /**
     * @When I bind with binddn :arg1 and password :arg2
     */
    public function iBindWithBinddnAndPassword($arg1, $arg2)
    {
      try {
        return $this->ldap->bind($arg1, $arg2);
      } catch(\Exception $e) {
        $this->appendException($e);
        return false;
      }
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
      $matchedExceptions = array_filter($this->exceptions, function($e) use ($arg1) {
        return ($e instanceof LdapException) && $e->getMessage() == $arg1;
      });
      $expectedException = LdapException::class . '("' . $arg1 .'")';
      $foundExceptions = array_map(function($e) {
        return get_class($e) . '("' . $e->getMessage() . '")';
        }, $this->exceptions
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
      Assert::assertNull($this->lastException());
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
}
