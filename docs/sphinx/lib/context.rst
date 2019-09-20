.. index::
   single: Context
   single: Lib; Context


The Context Library
-------------------

The Context library provides functionality similar to that of Python's
with-statement contexts.

Installation
^^^^^^^^^^^^

.. code-block:: shell

   php composer.phar require "korowai/contextlib:dev-master"

Basic Usage
^^^^^^^^^^^

Import :func:`Korowai\\Lib\\Context\\with` function to the current scope

.. literalinclude:: ../examples/lib/context/basic_with_usage.php
   :start-after: [use]
   :lines: 1

Execute your code with an open file

.. literalinclude:: ../examples/lib/context/basic_with_usage.php
   :start-after: [withFopenDoFread]
   :lines: 1-3

The file gets automatically closed just before the return from the call.

:class:`Korowai\\Lib\\Context\\ContextManagerInterface`


.. toctree::
   :maxdepth: 1
   :hidden:
   :glob:

   context/*

.. <!--- vim: set syntax=rst spell: -->
