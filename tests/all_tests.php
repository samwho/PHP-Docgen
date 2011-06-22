<?php
// Load the simple test testing framework
require_once dirname(__FILE__) . '/../extlib/simpletest/autorun.php';
// Load the Docgen lazy loader
require_once dirname(__FILE__) . '/../lib/class.Docgen_LazyLoader.php';
// Load the Dwoo autoloader
require_once dirname(__FILE__) . '/../extlib/dwoo/dwooAutoload.php';
// Load all of the test cases
foreach(glob(dirname(__FILE__) . '/test_cases/TestOf*.php') as $test_case_file) {
    require_once $test_case_file;
}

// Load plugins into the tests. Not 100% sure loading all of them is a good
// idea yet but we'll see how it goes :)
Docgen_Plugins::loadAll();

// Create test suite
$all_tests = new TestSuite('All tests');

// Add test cases to test suite
$all_tests->add(new TestOfPlugins());
$all_tests->add(new TestOfCodeSearch());
$all_tests->add(new TestOfCommandLineUtils());
$all_tests->add(new TestOfParserUtils());
$all_tests->add(new TestOfHooks());
$all_tests->add(new TestOfLog());
$all_tests->add(new TestOfParser());

// Run tests with a TextReporter
$reporter = new TextReporter();
$all_tests->run($reporter);
