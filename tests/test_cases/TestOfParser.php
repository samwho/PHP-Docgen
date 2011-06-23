<?php

class TestOfParser extends UnitTestCase {
    private $parser;
    private $search;
    private $base_dir;

    public function setUp() {
        $this->search = new Docgen_CodeSearch();
        $this->parser = new Docgen_Parser();
        $this->base_dir = Docgen::baseDir();

    }

    public function tearDown() {
        unset($this->parser);
        unset($this->search);
        foreach(glob($this->base_dir . 'tests/build/*') as $file) {
            unlink($file);
        }
    }

    public function testAddClasses() {
        $this->search->findClasses($this->base_dir . 'tests/test_classes/TwoInOne.php');
        $this->parser->addClasses($this->search->getClassList());

        $this->assertEqual($this->parser->getClassList(), $this->search->getClassList());
    }

    /**
     * This test parses through all of the lib/ source files. It passes them through
     * the class_rst.tpl template and the point is to identify if there are any exceptions.
     *
     * It's kind of like a test run.
     */
    public function testParseSource() {
        if (!is_writable($this->base_dir . 'tests/build')) {
            echo "Source parsing test skipped. Can't write to tests/build.\n";
        }

        $files_to_parse = glob($this->base_dir . 'lib/*.php');

        $this->search->findClasses($this->base_dir . 'lib/*.php');
        $this->parser->addClasses($this->search->getClassList());

        $this->parser->parseAllToFile($this->base_dir . 'templates/rst/class.tpl',
            $this->base_dir . 'tests/build/:class_name.rst');

        $files_parsed = glob($this->base_dir . 'tests/build/*');
        $this->assertEqual(sizeof($files_to_parse), sizeof($files_parsed));
    }
}
