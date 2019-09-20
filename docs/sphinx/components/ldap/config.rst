.. index::
   :single: Ldap; Configuration
   :single: Components; Ldap; Configuration


Ldap Configuration
------------------

:class:`Korowai\\Component\\Ldap\\Ldap` instances are created according to
several configuration settings provided as a key => value ``$config`` array.
This ``$config`` array is actually passed to an adapter factory when creating
the supporting adapter instance. Some settings are "standard" and shall be
accepted by any adapter type. Other options may be specific to a particular
adapter (such as the
:class:`ExtLdap\\Adapter <Korowai\\Component\\Ldap\\Adapter\\ExtLdap\\Adapter>`).

.. list-table:: Configuration settings
   :header-rows: 1
   :widths: 1 1 3

   * - Option
     - Default
     - Description

   * - ``host``
     - ``'localhost'``
     - Host to connect to (server IP or hostname).

   * - ``uri``
     - ``null``
     - URI for LDAP connection. This may be specified alternativelly to
       ``host``, ``port`` and ``encryption``.

   * - ``encryption``
     - ``'none'``
     - Encription. Possible values: ``'none'``, ``'ssl'``.

   * - ``port``
     - ``389`` or ``636``
     - Server port to conect to. If ``'encryption'`` is ``'ssl'``, then ``636``
       is used. Otherwise default ``'port'`` is ``389``.

   * - ``options``
     - ``array()``
     - An array of additional connection options (adapter-specific).

The nested ``options`` for the default adapter
:class:`ExtLdap\\Adapter <Korowai\\Component\\Ldap\\Adapter\\ExtLdap\\Adapter>`
are listed in the following table

.. list-table:: Configuration options for :class:`ExtLdap\\Adapter <Korowai\\Component\\Ldap\\Adapter\\ExtLdap\\Adapter>`
   :header-rows: 1
   :widths: 2 1 5

   * - Option
     - Type
     - Description 

   * - ``deref``
     - ``int``
     - TODO

   * - ``sizelimit``
     - ``int``
     - TODO

   * - ``timelimit``
     - ``int``
     - TODO

   * - ``network_timeout``
     - ``int``
     - TODO

   * - ``protocol_version``
     - ``int``
     - TODO

   * - ``error_number``
     - ``int``
     - TODO

   * - ``referrals``
     - ``bool``
     - TODO

   * - ``restart``
     - ``bool``
     - TODO

   * - ``host_name``
     - ``string``
     - TODO

   * - ``error_string``
     - ``string``
     - TODO

   * - ``diagnostic_message``
     - ``string``
     - TODO

   * - ``matched_dn``
     - ``string``
     - TODO

   * - ``server_controls``
     - ``array``
     - TODO

   * - ``client_controls``
     - ``array``
     - TODO

   * - ``keepalive_idle``
     - ``int``
     - TODO

   * - ``keepalive_probes``
     - ``int``
     - TODO

   * - ``keepalive_interval``
     - ``int``
     - TODO

   * - ``sasl_mech``
     - ``string``
     - TODO

   * - ``sasl_realm``
     - ``string``
     - TODO

   * - ``sasl_authcid``
     - ``string``
     - TODO

   * - ``sasl_authzid``
     - ``string``
     - TODO

   * - ``tls_cacertdir``
     - ``string``
     - TODO

   * - ``tls_cacertfile``
     - ``string``
     - TODO

   * - ``tls_certfile``
     - ``string``
     - TODO

   * - ``tls_cipher_suite``
     - ``string``
     - TODO

   * - ``tls_crlcheck``
     - ``int``
     - TODO

   * - ``tls_crlfile``
     - ``string``
     - TODO

   * - ``tls_dhfile``
     - ``string``
     - TODO

   * - ``tls_keyfile``
     - ``string``
     - TODO

   * - ``tls_protocol_min``
     - ``int``
     - TODO

   * - ``tls_random_file``
     - ``string``
     - TODO

   * - ``tls_require_cert``
     - ``int``
     - TODO

.. <!--- vim: set syntax=rst spell: -->
