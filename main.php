<?php
require_once dirname(__FILE__) . '/lib/class.LazyLoader.php';
Plugins::loadAll();

$tu_dir = '/home/sam/Dropbox/code/PHP/ThinkUp/';

$search = new CodeSearch();
$classes = $search->findClasses($tu_dir . 'webapp/_lib/**/*.php');
$classes['__init__'] = $tu_dir . 'webapp/init.php';

$parser = new Parser($classes);
$parser->parseAll('templates/class_rst.tpl', $tu_dir . 'docs/source/reference/:class_name.rst');
$parser->generateTocTree($tu_dir . 'docs/source/reference/index.rst');

?>
