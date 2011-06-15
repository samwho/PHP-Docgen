{$class.name}
{regex_replace $class.name "/./" "="}

View `full source code <./source/{$class.name}.html>`_.

{if $class.interfaces}
Interfaces
----------
{foreach $class.interfaces interface}* `{$interface} <./{$interface}.html>`_{/foreach}
{/if}
{if $class.parent}
Inherits from `{$class.parent} <./{$class.parent}.html>`_.
{/if}

{if $class.children}
Children
--------

{foreach $class.children child}
* `{$child} <./{$child}.html>`_
{/foreach}
{/if}

{if $class.docblock}
{$class.docblock}
{else}
There is no documentation for this class.
{/if}

{if $class.properties}
Properties
----------

{foreach $class.properties property}{include $docgen.template.rst.property}{/foreach}
{/if}

{if $class.inherited_properties}
Inherited Properties
--------------------

{foreach $class.inherited_properties property}{include $docgen.template.rst.property}{/foreach}
{/if}

{if $class.methods}
Methods
-------

{foreach $class.methods method}{include $docgen.template.rst.method}{/foreach}
{/if}
{if $class.inherited_methods}
Inherited Methods
-----------------

{foreach $class.inherited_methods method}{include $docgen.template.rst.method}{/foreach}
{/if}
{* The {disqus} tag is provided by the disqus plugin. If you disable that plugin, you will need to remove this tag. *}
{disqus}
