.. index::
   single: Ldap; Exceptions
   single: Components; Ldap; Exceptions


Ldap Exceptions
---------------

Ldap component uses exceptions quite extensively. Most of errors are reported
to caller by throwing exceptions.

Custom exceptions of the Ldap component are defined in
:namespace:`Korowai\\Component\\Ldap\\Exception <Korowai\\Component\\Ldap\\Exception>`
namespace. The following exception classes are currently defined:

.. list-table:: Ldap component's exceptions
   :header-rows: 1
   :widths: 1 1 2

   * - Exception
     - Base Exception
     - Thrown when
   * - :class:`Korowai\\Component\\Ldap\\Exception\\AttributeException`
     - `OutOfRangeException <https://php.net/OutOfRangeException>`_
     - accessing nonexistent attribute of an LDAP :class:`Korowai\\Component\\Ldap\\Entry`
   * - :class:`Korowai\\Component\\Ldap\\Exception\\LdapException`
     - `RuntimeException <https://php.net/RuntimeException>`_
     - an error occurs during an LDAP operation


AttributeException
^^^^^^^^^^^^^^^^^^

Derived from `OutOfRangeException <https://php.net/OutOfRangeException>`_.
It's being thrown when accessing nonexistent attribute of an
LDAP :class:`Korowai\\Component\\Ldap\\Entry`. For example

.. code-block:: php

   $entry->getAttribute('nonexistent'); // This will throw AttributeException


LdapException
^^^^^^^^^^^^^

Derived from `RuntimeException <https://php.net/RuntimeException>`_. It's being
thrown when an LDAP operation fails. The exception message and code are copied
from the LDAP error message and code.

.. code-block:: php

   // This may throw LdapException with "No such object"
   $ldap->query('dc=nonexistent,dc=com', 'objectclass=*');


To handle particular LDAP errors in an application, exception code may be used

.. code-block:: php

   use Korowai\Component\Ldap\Exception\LdapException;
   // ...
   try {
      $result = $ldap->query('dc=nonexistent,dc=com', 'objectclass=*');
   } catch(LdapException $e) {
      if($e->getCode() == 0x20) { /* No such object */
         $result = null;
      } else {
         throw $e;
      }
   }

Standard LDAP result codes (including error codes) are defined in several
documents including `RFC 4511`_, `RFC 3928`_, `RFC 3909`_, `RFC 4528`_, and
`RFC 4370`_. An authoritative source of LDAP result codes is the `IANA registry`_.
A useful list of LDAP return codes may also be found on `LDAP Wiki`_.


.. _IANA registry: https://www.iana.org/assignments/ldap-parameters/ldap-parameters.xhtml#ldap-parameters-6
.. _LDAP Wiki: https://ldapwiki.com/wiki/LDAP%20Result%20Codes
.. _RFC 4511: https://tools.ietf.org/html/rfc4511#section-4.1.9
.. _RFC 3928: http://www.iana.org/go/rfc3928#section-3.5
.. _RFC 3909: http://www.iana.org/go/rfc3909#section-2.3
.. _RFC 4528: https://tools.ietf.org/html/rfc4528#section-5.3
.. _RFC 4370: https://tools.ietf.org/html/rfc4370

.. <!--- vim: set syntax=rst spell: -->
