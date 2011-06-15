{$name}
{regex_replace $name "/./" "="}

View `full source code <./source/{$name}.html>`_.

{if $interfaces}
Interfaces
----------
{foreach $interfaces interface}* `{$interface} <./{$interface}.html>`_{/foreach}
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

{foreach $properties property}{include 'templates/rst/property_rst.tpl'}{/foreach}
{/if}

{if $inherited_properties}
Inherited Properties
--------------------

{foreach $inherited_properties property}{include 'templates/rst/property_rst.tpl'}{/foreach}
{/if}

{if $methods}
Methods
-------

{foreach $methods method}{include 'templates/rst/method_rst.tpl'}{/foreach}
{/if}
{if $inherited_methods}
Inherited Methods
-----------------

{foreach $inherited_methods method}{include 'templates/rst/method_rst.tpl'}{/foreach}
{/if}
{* The {disqus} tag is provided by the disqus plugin. If you disable that plugin, you will need to remove this tag. *}
{disqus}
