<?php
// Load the simple test testing framework
require_once dirname(__FILE__) . '/../extlib/simpletest/autorun.php';
// Load the Docgen lazy loader
require_once dirname(__FILE__) . '/../lib/class.Docgen_LazyLoader.php';
// Load all of the test cases
foreach(glob(dirname(__FILE__) . '/test_cases/TestOf*.php') as $test_case_file) {
    require_once $test_case_file;
}

// Create test suite
$all_tests = new TestSuite('All tests');

// Add test cases to test suite
$all_tests->add(new TestOfPlugins());
$all_tests->add(new TestOfCodeSearch());
$all_tests->add(new TestOfCommandLineUtils());
$all_tests->add(new TestOfParserUtils());
$all_tests->add(new TestOfHooks());
$all_tests->add(new TestOfLog());

// Run tests with a TextReporter
$reporter = new TextReporter();
$all_tests->run($reporter);
