<?php

class Foo {
    /**
     * Class constant docblock. @this isn't a tag.
     */
    const CLASS_CONSTANT = 15;

    /**
     * @var string A string.
     */
    private $instance_var = 'I\'m private.';

    /**
     * A class var.
     *
     * With a multiline comment.
     *
     * @var int
     */
    public static $class_var = 42;
}
