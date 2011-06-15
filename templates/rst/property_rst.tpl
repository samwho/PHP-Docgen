{$property.name}
{regex_replace $property.name "/./" "~"}
{if $property.is_inherited}
Inherited from `{$property.declaring_class} <./{$property.declaring_class}.html>`_.
{/if}

{$property.docblock}

