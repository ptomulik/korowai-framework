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
    Given I am connected using JSON config <config>
    When I bind with binddn <binddn> and password <password>
    Then I should see no exception
    And I should be bound

    Examples:
      |         config                        |           binddn             | password  |
      | '{"uri":"ldap://ldap-service"}'       | 'cn=admin,dc=example,dc=org' | 'admin'   |
      | '{"host":"ldap-service"}'             | 'cn=admin,dc=example,dc=org' | 'admin'   |
      | '{"host":"ldap-service","port":389}'  | 'cn=admin,dc=example,dc=org' | 'admin'   |

  Scenario Outline: LDAP link creation failure
    Given I am disconnected
    When I create ldap link with JSON config <config>
    Then I should see ldap exception with message "ldap_connect(): Could not create session handle: Bad parameter to an ldap routine"
    And I should have no valid LDAP link

    Examples:
      |             config             |
      | '{"uri":"foop://invalid-uri"}' |

  Scenario Outline: Unsuccessful bind because of connection problems
    Given I am connected using JSON config <config>
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

    Examples:
      |         uri           |           binddn             | password  |
      | "ldap://ldap-service" | "cn=admin,dc=example,dc=org" | "nimda"   |
      | "ldap://ldap-service" | "cn=nimda,dc=example,dc=org" | "admin"   |
