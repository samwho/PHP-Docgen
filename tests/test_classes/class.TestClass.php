<?php
class TestClass {
    const A_CONSTANT = 12;

    /**
     * Stay out ;)
     */
    private $property;

    /**
     * tagged docblock.
     *
     * @var string
     */
    public static $staying_here = 'yup';

    /**
     * This is a comment.
     *
     * With another line.
     *
     * @param string $param This is a tag.
     * @param string $param2 Another tag.
     */
    public function test($param, $param2 = 'default') {

    }

    private function test_private() {

    }

    function just_a_func() {

    }
}

