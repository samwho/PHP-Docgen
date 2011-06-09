{$name}
{regex_replace $name "/./" "="}
{if $parent}
Inherits from `{$parent.name} <./{$parent.name}.html>`_.
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
