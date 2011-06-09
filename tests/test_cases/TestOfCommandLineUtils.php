<?php

class TestOfCommandLineUtils extends UnitTestCase {
    public function testParseArgs() {
        $args = array(
            basename(__FILE__), // add the script name
            'hello',
            '-a',
            '-bc',
            '--boolean',
            '--foo=bar',
            'some_string'
        );

        $parsed_args = Docgen_CommandLineUtils::parseArgs($args);

        $expected = array(
            'a' => true,
            'b' => true,
            'c' => true,
            'boolean' => true,
            'foo' => 'bar',
            0 => 'hello',
            1 => 'some_string'
        );

        $this->assertEqual($parsed_args, $expected);
    }
}
