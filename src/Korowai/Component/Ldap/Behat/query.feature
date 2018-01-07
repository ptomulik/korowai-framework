Feature: Query
##
##  Scenario: Successful read of a single entry
##    Given I am bound to uri "ldap://ldap-service" as dn "cn=admin,dc=example,dc=org" with password "admin"
##    When I query dn "dc=example,dc=org" with filter "objectclass=*"
##    Then I should see no exception
##    And I should get single entry
