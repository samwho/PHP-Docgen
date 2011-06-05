{$name}
{regex_replace $name "/./" "="}
{if $docblock}
{$docblock}
{else}
There is no documentation for this class.
{/if}
Methods
-------

{foreach $methods method}
{$method.name}
{regex_replace $method.name "/./" "~"}

{$method.docblock}

{/foreach}

Properties
----------

{foreach $properties property}
{$property.name}
{regex_replace $property.name "/./" "~"}

{$property.docblock}

{/foreach}
