<?php
/* [use] */
use Korowai\Component\Ldap\Ldap;

$config = array('uri' => 'ldap://ldap-service');
$ldap = Ldap::createWithConfig($config);

/* [bind] */
/* Bind to 'cn=admin,dc=example,dc=org' using password 'admin'. */
$ldap->bind('cn=admin,dc=example,dc=org', 'admin');

/* [query] */
/* The returned result implements ResultInterface. */
$result = $ldap->query('ou=people,dc=example,dc=org', 'objectclass=*');

/* [foreach] */
foreach($result as $dn => $entry) {
  print($dn . " => "); print_r($entry->getAttributes());
}

/* [getEntries] */
$entries = $result->getEntries();

/* [entry] */
$entry = $entries['uid=jsmith,ou=people,dc=example,dc=org'];

/* [setAttribute] */
$entry->setAttribute('uidnumber', array(1234));

/* [update] */
$ldap->update($entry);
