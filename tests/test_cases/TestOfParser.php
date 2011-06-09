<?php

class TestOfParser extends UnitTestCase {
    private $parser;
    private $search;

    public function setUp() {
        $this->search = new Docgen_CodeSearch();
        $this->parser = new Docgen_Parser();
    }

    public function tearDown() {
        unset($this->parser);
        unset($this->search);
    }

    public function testAddClasses() {

    }
}
