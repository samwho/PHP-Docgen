<?php

/**
 * This test mainly checks that all of the directories point to somewhere
 * meaningful. Just in case :)
 */
class TestOfDocgen extends UnitTestCase {
    public function testBaseDir() {
        $this->assertTrue(file_exists(Docgen::baseDir()));
    }
    public function testTemplateDir() {
        $this->assertTrue(file_exists(Docgen::templateDir()));
    }
    public function testPluginDir() {
        $this->assertTrue(file_exists(Docgen::pluginDir()));
    }
    public function testLibDir() {
        $this->assertTrue(file_exists(Docgen::libDir()));
    }

    public function testExtlibDir() {
        $this->assertTrue(file_exists(Docgen::extlibDir()));
    }
    public function testTemplateRstDir() {
        $this->assertTrue(file_exists(Docgen::templateRstDir()));
    }
    public function testVersion() {
        $this->assertTrue(is_string(Docgen::VERSION));
    }
}
