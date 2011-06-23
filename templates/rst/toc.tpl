{$title}
{regex_replace $title "/./" "="}

{$message}

.. toctree::
   :maxdepth: 3

{foreach $classes class}
   {$class.name}
{/foreach}
