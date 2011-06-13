{$method.name}
{regex_replace $method.name "/./" "~"}
{if $method.is_inherited}
Inherited from `{$method.class_name} <./{$method.class_name}.html>`_.
{/if}
{if $method.tags}
{foreach $method.tags tag}
* **@{$tag.name}** {regex_replace $tag.contents "/\n/" " "}
{/foreach}
{/if}
{$method.docblock}

.. code-block:: php5

    {"<?php"}
{indent $method.source}

