{$name}
{regex_replace $name "/./" "="}

View `full source code <./source/{$name}.html>`_.

{if $interfaces}
Interfaces
----------
{foreach $interfaces interface}
* `{$interface} <./{$interface}.html>`_
{/foreach}
{/if}
{if $parent}
Inherits from `{$parent} <./{$parent}.html>`_.
{/if}
{if $children}
Children
--------

{foreach $children child}
* `{$child} <./{$child}.html>`_
{/foreach}
{/if}
{if $docblock}
{$docblock}
{else}
There is no documentation for this class.
{/if}

{if $properties}
Properties
----------

{foreach $properties property}
{$property.name}
{regex_replace $property.name "/./" "~"}

{$property.docblock}

{/foreach}
{/if}
{if $inherited_properties}
Inherited Properties
--------------------

{foreach $inherited_properties property}
{$property.name}
{regex_replace $property.name "/./" "~"}

{$property.docblock}

{/foreach}
{/if}
{if $methods}
Methods
-------

{foreach $methods method}
{$method.name}
{regex_replace $method.name "/./" "~"}
{if $method.tags}
{foreach $method.tags tag}
* **@{$tag.name}** {regex_replace $tag.contents "/\n/" " "}
{/foreach}
{/if}
{$method.docblock}

.. code-block:: php5

    {"<?php"}
{indent $method.source}

{/foreach}
{/if}
{if $inherited_methods}
Inherited Methods
-----------------

{foreach $inherited_methods method}
{$method.name}
{regex_replace $method.name "/./" "~"}
Inherited from `{$method.class_name} <./{$method.class_name}.html>`_.

{if $method.tags}
{foreach $method.tags tag}
* **@{$tag.name}** {regex_replace $tag.contents "/\n/" " "}
{/foreach}
{/if}
{$method.docblock}

.. code-block:: php5

    {"<?php"}
{indent $method.source}

{/foreach}
{/if}
