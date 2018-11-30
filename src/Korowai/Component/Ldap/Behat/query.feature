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

  Scenario: Successful query with the 'scope=base' option
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
