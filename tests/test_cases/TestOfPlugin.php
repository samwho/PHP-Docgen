<?php

class TestOfPlugins extends UnitTestCase {

    public function testLoadAllPlugins() {
        Docgen_Plugins::loadAll($testing = true);

        $plugin_files = Docgen_Plugins::getLoadedPlugins();

        // Because plugin files get run through realpath() before being loaded,
        // we need to run the globbed files through realpath() to make sure the
        // comparison works.
        $comparison_files = array();
        foreach(glob(dirname(__FILE__) . '/../test_plugins/*.php') as $file) {
            $comparison_files[] = realpath($file);
        }

        $this->assertEqual($plugin_files, $comparison_files);
    }

}
