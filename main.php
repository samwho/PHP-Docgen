<?php
require_once '/home/sam/Dropbox/code/PHP/ThinkUp/webapp/_lib/model/class.Post.php';
require_once 'lib/class.Loader.php';

$class = new ClassParser('Post');
$methods = $class->getMethods();

foreach($methods as $method) {
    echo $method->class . "->" . $method->name . "\n";
    print_r($method->getDocTags());
}

?>
