Feature: Simple bind

  Scenario Outline: Successful bind using URI, binddn and password
    Given I am connected to uri <uri>
    When I bind with binddn <binddn> and password <password>
    Then I should see no exception
    And I should be bound

    Examples:
      |         uri           |           binddn             | password  |
      | "ldap://ldap-service" | "cn=admin,dc=example,dc=org" | "admin"   |

  Scenario Outline: Successful bind using config, binddn and password
    Given I am connected using config <config>
    When I bind with binddn <binddn> and password <password>
    Then I should see no exception
    And I should be bound

    Examples:
      |         config                        |           binddn             | password  |
      | '{"uri":"ldap://ldap-service"}'       | 'cn=admin,dc=example,dc=org' | 'admin'   |
      | '{"host":"ldap-service"}'             | 'cn=admin,dc=example,dc=org' | 'admin'   |
      | '{"host":"ldap-service","port":389}'  | 'cn=admin,dc=example,dc=org' | 'admin'   |

  Scenario Outline: Successful anonymous bind
    Given I am connected to uri <uri>
    When I bind with binddn <binddn> and password <password>
    Then I should see no exception
    And I should be bound

    Examples:
      |         uri           |           binddn             | password  |
      | "ldap://ldap-service" |       ""                     |    ""     |

  Scenario Outline: LDAP link creation failure
    Given I am disconnected
    When I create ldap link with config <config>
    Then I should see ldap exception with message "ldap_connect(): Could not create session handle: Bad parameter to an ldap routine"
    And I should have no valid LDAP link

    Examples:
      |             config             |
      | '{"uri":"foop://invalid-uri"}' |

  Scenario Outline: Unsuccessful bind because of connection problems
    Given I am connected using config <config>
    When I bind with binddn <binddn> and password <password>
    Then I should see ldap exception with message "Can't contact LDAP server"
    And I should have a valid ldap link
    And I should not be bound

    Examples:
      |         config                        |           binddn             | password  |
      | '{"host":"invalid-host"}'             | 'cn=admin,dc=example,dc=org' | 'admin'   |
      | '{"host":"ldap-service","port":111}'  | 'cn=admin,dc=example,dc=org' | 'admin'   |

  Scenario Outline: Unsuccessful bind because of invalid credentials
    Given I am connected to uri <uri>
    When I bind with binddn <binddn> and password <password>
    Then I should see ldap exception with message "Invalid credentials"
    And I should not be bound

    Examples:
      |         uri           |           binddn             | password  |
      | "ldap://ldap-service" | "cn=admin,dc=example,dc=org" | "nimda"   |
      | "ldap://ldap-service" | "cn=nimda,dc=example,dc=org" | "admin"   |

  Scenario Outline: Unsuccessful bind because of missing password
    Given I am connected to uri <uri>
    When I bind with binddn <binddn>
    Then I should see ldap exception with message "Server is unwilling to perform"
    And I should not be bound

    Examples:
      |         uri           |           binddn             |
      | "ldap://ldap-service" | "cn=admin,dc=example,dc=org" |


  Scenario Outline: Binding without arguments
    Given I am connected to uri <uri>
    When I bind without arguments
    Then I should see no exception
    And I should be bound

    Examples:
      |         uri           |
      | "ldap://ldap-service" |
