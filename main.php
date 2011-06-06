<?php
require_once dirname(__FILE__) . '/lib/class.LazyLoader.php';
Plugins::loadAll();

$classes = json_decode(exec('./tu_specific/gen_class_list'), $assoc = true);

$parser = new Parser($classes);
$parser->parseAll('templates/class_rst.tpl', '/home/sam/Dropbox/code/PHP/ThinkUp/docs/source/reference/:class_name.rst');
$parser->generateTocTree('/home/sam/Dropbox/code/PHP/ThinkUp/docs/source/reference/index.rst');

?>
