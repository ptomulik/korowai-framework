@initDbBeforeFeature
@initDbAfterFeature
Feature: Query

  Scenario: Successful query for an empty yet subtree
    Given I am connected to uri "ldap://ldap-service"
    And I am bound with binddn "cn=admin,dc=example,dc=org" and password "admin"
    When I query with basedn "dc=empty,dc=example,dc=org" and filter "(objectclass=*)"
    Then I should see no exception
    And I should have last result entries
        """
        {
          "dc=empty,dc=example,dc=org": {
            "objectclass": ["top", "dcObject", "organization"],
            "dc": ["empty"],
            "o": ["Empty, Example Org."] }
        }
        """

  Scenario: Successful query for a non-empty subtree
    Given I am connected to uri "ldap://ldap-service"
    And I am bound with binddn "cn=admin,dc=example,dc=org" and password "admin"
    When I query with basedn "ou=people,dc=example,dc=org" and filter "(objectclass=*)"
    Then I should see no exception
    And I should have last result entries
        """
        {
          "ou=people,dc=example,dc=org": {
            "objectclass": ["top", "organizationalUnit"],
            "ou": ["people"] },
          "uid=jsmith,ou=people,dc=example,dc=org": {
            "uid": ["jsmith"],
            "uidnumber": ["5678"],
            "objectclass": ["top", "inetOrgPerson", "shadowAccount", "posixAccount"],
            "cn": ["John Smith"],
            "displayname": ["John Smith"],
            "gecos": ["John Smith,,,"],
            "gidnumber": ["5000"],
            "givenname": ["John"],
            "sn": ["Smith"],
            "homedirectory": ["/home/jsmith"],
            "initials": ["J. S"],
            "loginshell": ["/bin/bash"],
            "mail": ["jsmith@example.org"],
            "roomnumber": ["114"],
            "telephonenumber": ["+12 34 567 8908"],
            "userpassword": ["secret"] }
        }
        """

  Scenario: Successful query with the 'scope=base' option and default filter
    Given I am connected to uri "ldap://ldap-service"
    And I am bound with binddn "cn=admin,dc=example,dc=org" and password "admin"
    When I query with basedn "ou=people,dc=example,dc=org", filter "(objectclass=*)" and options '{"scope": "base"}'
    Then I should see no exception
    And I should have last result entries
        """
        {
          "ou=people,dc=example,dc=org": {
            "objectclass": ["top", "organizationalUnit"],
            "ou": ["people"] }
        }
        """

  Scenario: Successful query with the 'scope=one' option and default filter
    Given I am connected to uri "ldap://ldap-service"
    And I am bound with binddn "cn=admin,dc=example,dc=org" and password "admin"
    When I query with basedn "dc=example,dc=org", filter "(objectclass=*)" and options '{"scope": "one"}'
    Then I should see no exception
    And I should have last result entries
        """
        {
          "ou=people,dc=example,dc=org": {
            "objectclass": ["top", "organizationalUnit"],
            "ou": ["people"] },
          "dc=empty,dc=example,dc=org": {
            "objectclass": ["top", "dcObject", "organization"],
            "dc": ["empty"],
            "o": ["Empty, Example Org."]},
          "dc=subtree,dc=example,dc=org": {
            "objectclass": ["top", "dcObject", "organization"],
            "dc": ["subtree"],
            "o": ["Subtree, Example Org."]},
          "cn=admin,dc=example,dc=org": {
            "objectclass": ["simpleSecurityObject", "organizationalRole"],
            "cn": ["admin"],
            "description": ["LDAP administrator"],
            "userpassword": ["admin"]}
        }
        """

  Scenario: Successful query with the 'scope=one' option and custom filter
    Given I am connected to uri "ldap://ldap-service"
    And I am bound with binddn "cn=admin,dc=example,dc=org" and password "admin"
    When I query with basedn "dc=example,dc=org", filter "(objectclass=dcObject)" and options '{"scope": "one"}'
    Then I should see no exception
    And I should have last result entries
        """
        {
          "dc=empty,dc=example,dc=org": {
            "objectclass": ["top", "dcObject", "organization"],
            "dc": ["empty"],
            "o": ["Empty, Example Org."]},
          "dc=subtree,dc=example,dc=org": {
            "objectclass": ["top", "dcObject", "organization"],
            "dc": ["subtree"],
            "o": ["Subtree, Example Org."]}
        }
        """
