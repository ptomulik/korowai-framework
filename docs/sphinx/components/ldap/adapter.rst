.. index::
   :single: Ldap; Adapter
   :single: Components; Ldap; Adapter

Ldap Adapters
-------------

The Ldap component uses adapters to interact with the actual LDAP
implementation. An adapter is a class that converts the interface of that
particular implementation to the unified interface defined by
:class:`Korowai\\Component\\Ldap\\Adapter\\AdapterInterface`. This pattern
allows for different LDAP implementations to be used by the Ldap component in
a pluggable manner.


Every :class:`Korowai\\Component\\Ldap\\Ldap` instance wraps an instance of
:class:`Korowai\\Component\\Ldap\\Adapter\\AdapterInterface` (adapter) and
interacts with the LDAP back-end through the adapter. The adapter instance is
feed to :class:`Korowai\\Component\\Ldap\\Ldap`'s constructor when it's being
created. The whole process of adapter instantiation is done behind the scenes.

Although not recommended, "manual" adapter instantiation is possible:

.. code-block:: php

   use Korowai\Component\Ldap\Ldap;
   use Korowai\Component\Ldap\Adapter\ExtLdap\LdapLink;
   use Korowai\Component\Ldap\Adapter\ExtLdap\Adapter;

   $link = LdapLink::connect('ldap://ldap-service');
   $link->set_option(LDAP_OPT_PROTOCOL_VERSION, 3);
   $adapter = new Adapter($link);
   $ldap = new Ldap($adapter);
   // ...
   $ldap->bind('cn=admin,dc=example,dc=org', 'admin');

In the above code, default adapter, the
:class:`ExtLdap\\Adapter <Korowai\\Component\\Ldap\\Adapter\\ExtLdap\\Adapter>`
is used. This adapter uses the built-in `PHP ldap extension`_ via the
:class:`Korowai\\Component\\Ldap\\Adapter\\ExtLdap\\LdapLink` class.
Other adapter classes can be created to support other LDAP implementations.

Adapter Factory
^^^^^^^^^^^^^^^

An adapter class is accompanied with its adapter factory. This configurable
object creates adapter instances. Adapter factories implement
:class:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface` which
defines two methods:
:method:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface::configure`
and
:method:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface::createAdapter`.
New adapter instances are created with
:method:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface::createAdapter`
according to configuration options provided earlier to
:method:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface::configure`.

Adapter factory may be specified when creating an
:class:`Korowai\\Component\\Ldap\\Ldap` instance. For this purpose,
a preconfigured instance of the
:class:`Korowai\\Component\\Ldap\\Adapter\\AdapterFactoryInterface`
shall be provided to :class:`Korowai\\Component\\Ldap\\Ldap`'s static method
:method:`Korowai\\Component\\Ldap\\Ldap::createWithAdapterFactory`:

.. code-block:: php

   use Korowai\Component\Ldap\Ldap;
   use Korowai\Component\Ldap\Adapter\ExtLdap\AdapterFactory;

   $config = array('uri' => 'ldap://ldap-service');
   $factory = new AdapterFactory($config);
   $ldap = Ldap::createWithAdapterFactory($factory);

Alternatively, factory class name may be passed to
:method:`Korowai\\Component\\Ldap::createWithConfig`
method:

.. code-block:: php

   use Korowai\Component\Ldap\Ldap;
   use Korowai\Component\Ldap\Adapter\ExtLdap\AdapterFactory;

   $config = array('uri' => 'ldap://ldap-service');
   $ldap = Ldap::createWithConfig($config, AdapterFactory::class);

In this case, a temporary instance of adapter factory is created internally,
configured with ``$config`` and then used to create the actual adapter
instance for :class:`Korowai\\Component\\Ldap\\Ldap`.


.. _PHP ldap extension: http://php.net/manual/en/book.ldap.php

.. <!--- vim: set syntax=rst spell: -->
